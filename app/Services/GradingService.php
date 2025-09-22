<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\Question;
use App\Jobs\AutoGradeExamAttempts;
use App\Jobs\RecalculateExamResults;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GradingService
{
    /**
     * Auto-grade all MCQ and exact-match short answers for an exam
     */
    public function autoGradeExam(Exam $exam, ?int $questionId = null): int
    {
        $gradedCount = 0;

        $query = ExamAnswer::whereHas('attempt', function ($q) use ($exam) {
            $q->where('exam_id', $exam->id)
                ->where('status', 'completed');
        })
            ->whereNull('points_awarded')
            ->with(['question', 'question.options', 'attempt']);

        if ($questionId) {
            $query->where('question_id', $questionId);
        }

        $answers = $query->get();

        DB::transaction(function () use ($answers, &$gradedCount) {
            foreach ($answers as $answer) {
                if ($this->autoGradeAnswer($answer)) {
                    $gradedCount++;
                }
            }
        });

        // Recalculate attempt scores for affected attempts
        $attemptIds = $answers->pluck('exam_attempt_id')->unique();
        foreach ($attemptIds as $attemptId) {
            $attempt = ExamAttempt::find($attemptId);
            if ($attempt) {
                $this->recalculateAttemptScore($attempt);
            }
        }

        return $gradedCount;
    }

    /**
     * Auto-grade a single answer
     */
    public function autoGradeAnswer(ExamAnswer $answer): bool
    {
        $question = $answer->question;
        $maxPoints = $this->getMaxPointsForQuestion($question, $answer->attempt->exam_id);

        $points = 0;
        $autoGraded = false;

        switch ($question->type) {
            case 'mcq':
                $points = $this->gradeMCQAnswer($answer, $maxPoints);
                $autoGraded = true;
                break;

            case 'short':
                $points = $this->gradeShortAnswer($answer, $maxPoints);
                $autoGraded = true;
                break;

            case 'long':
                // Cannot auto-grade long answers
                return false;
        }

        if ($autoGraded) {
            $answer->update([
                'points_awarded' => $points,
                'graded_at' => now(),
                'graded_by' => null, // System graded
                'auto_graded' => true,
            ]);
        }

        return $autoGraded;
    }

    /**
     * Grade MCQ answer
     */
    protected function gradeMCQAnswer(ExamAnswer $answer, float $maxPoints): float
    {
        if (!$answer->answer_text) {
            return 0;
        }

        $correctOption = $answer->question->options()
            ->where('is_correct', true)
            ->first();

        if (!$correctOption) {
            return 0;
        }

        return $answer->answer_text === $correctOption->option_text ? $maxPoints : 0;
    }

    /**
     * Grade short answer (exact match or keyword-based)
     */
    protected function gradeShortAnswer(ExamAnswer $answer, float $maxPoints): float
    {
        if (!$answer->answer_text) {
            return 0;
        }

        $question = $answer->question;
        $studentAnswer = trim(strtolower($answer->answer_text));

        // Get correct answers (could be stored in question metadata or options)
        $correctAnswers = $this->getCorrectAnswersForShortQuestion($question);

        if (empty($correctAnswers)) {
            return 0; // Cannot auto-grade without correct answers
        }

        // Exact match
        foreach ($correctAnswers as $correctAnswer) {
            if ($studentAnswer === trim(strtolower($correctAnswer))) {
                return $maxPoints;
            }
        }

        // Keyword-based partial grading (if enabled)
        $keywordScore = $this->gradeByKeywords($studentAnswer, $correctAnswers, $maxPoints);

        return $keywordScore;
    }

    /**
     * Get correct answers for short questions
     */
    protected function getCorrectAnswersForShortQuestion(Question $question): array
    {
        // Try to get from question metadata first
        $metadata = $question->metadata ?? [];

        if (isset($metadata['correct_answers'])) {
            return (array) $metadata['correct_answers'];
        }

        // Try to get from options table (if used for short answers)
        $correctOptions = $question->options()
            ->where('is_correct', true)
            ->pluck('option_text')
            ->toArray();

        return $correctOptions;
    }

    /**
     * Grade by keywords with partial scoring
     */
    protected function gradeByKeywords(string $studentAnswer, array $correctAnswers, float $maxPoints): float
    {
        $bestScore = 0;

        foreach ($correctAnswers as $correctAnswer) {
            $correctAnswer = trim(strtolower($correctAnswer));
            $keywords = explode(' ', $correctAnswer);
            $matchCount = 0;

            foreach ($keywords as $keyword) {
                $keyword = trim($keyword);
                if (strlen($keyword) > 2 && strpos($studentAnswer, $keyword) !== false) {
                    $matchCount++;
                }
            }

            if (count($keywords) > 0) {
                $score = ($matchCount / count($keywords)) * $maxPoints;
                $bestScore = max($bestScore, $score);
            }
        }

        // Only award partial points if at least 50% keywords match
        return $bestScore >= ($maxPoints * 0.5) ? $bestScore : 0;
    }

    /**
     * Recalculate the total score for an attempt
     */
    public function recalculateAttemptScore(ExamAttempt $attempt): void
    {
        $totalAnswers = $attempt->answers->count();
        $totalGradedAnswers = $attempt->answers->where('is_graded', true);
        $totalScore = $totalGradedAnswers->sum('marks_awarded');

        $totalScore = max(0, $totalScore);
        $percentageScore = floor(($totalScore / $attempt->exam->total_marks) * 100);

        $attempt->update([
            'total_score' => $totalScore,
            'percentage_score' => $percentageScore,
            'status' => $totalAnswers === $totalGradedAnswers->count() ? 'graded' : 'submitted'
        ]);
    }

    /**
     * Get maximum points for a question in a specific exam
     */
    protected function getMaxPointsForQuestion(Question $question, int $examId): float
    {
        $examQuestion = DB::table('exam_questions')
            ->where('exam_id', $examId)
            ->where('question_id', $question->id)
            ->first();

        return $examQuestion ? (float) $examQuestion->points : 1.0;
    }

    /**
     * Bulk grade multiple answers with the same score
     */
    public function bulkGradeAnswers(array $answerIds, float $pointsAwarded, ?string $feedback = null): int
    {
        $gradedCount = 0;

        DB::transaction(function () use ($answerIds, $pointsAwarded, $feedback, &$gradedCount) {
            $answers = ExamAnswer::whereIn('id', $answerIds)
                ->with('attempt')
                ->get();

            $affectedAttempts = collect();

            foreach ($answers as $answer) {
                $answer->update([
                    'points_awarded' => $pointsAwarded,
                    'feedback' => $feedback,
                    'graded_at' => now(),
                    'graded_by' => auth()->id(),
                ]);

                $affectedAttempts->push($answer->attempt);
                $gradedCount++;
            }

            // Recalculate attempt scores
            $affectedAttempts->unique('id')->each(function ($attempt) {
                $this->recalculateAttemptScore($attempt);
            });
        });

        return $gradedCount;
    }

    /**
     * Reset grading for an answer
     */
    public function resetAnswerGrading(ExamAnswer $answer): void
    {
        $answer->update([
            'points_awarded' => null,
            'feedback' => null,
            'graded_at' => null,
            'graded_by' => null,
            'auto_graded' => false,
        ]);

        $this->recalculateAttemptScore($answer->attempt);
    }

    /**
     * Get grading statistics for an exam
     */
    public function getGradingStatistics(Exam $exam): array
    {
        $attempts = $exam->attempts()->where('status', 'completed')->get();

        $totalAnswers = 0;
        $gradedAnswers = 0;
        $autoGradedAnswers = 0;

        foreach ($attempts as $attempt) {
            $answers = $attempt->answers()->get();
            $totalAnswers += $answers->count();
            $gradedAnswers += $answers->whereNotNull('points_awarded')->count();
            $autoGradedAnswers += $answers->where('auto_graded', true)->count();
        }

        return [
            'total_attempts' => $attempts->count(),
            'total_answers' => $totalAnswers,
            'graded_answers' => $gradedAnswers,
            'ungraded_answers' => $totalAnswers - $gradedAnswers,
            'auto_graded_answers' => $autoGradedAnswers,
            'manual_graded_answers' => $gradedAnswers - $autoGradedAnswers,
            'grading_progress' => $totalAnswers > 0 ? ($gradedAnswers / $totalAnswers) * 100 : 0,
        ];
    }

    /**
     * Get suggested grades for similar answers
     */
    public function getSuggestedGrades(ExamAnswer $targetAnswer): array
    {
        $question = $targetAnswer->question;
        $exam = $targetAnswer->attempt->exam;

        // Find similar answers for the same question in the same exam
        $similarAnswers = ExamAnswer::whereHas('attempt', function ($query) use ($exam) {
            $query->where('exam_id', $exam->id)
                ->where('status', 'completed');
        })
            ->where('question_id', $question->id)
            ->where('id', '!=', $targetAnswer->id)
            ->whereNotNull('points_awarded')
            ->get();

        $suggestions = [];

        foreach ($similarAnswers as $answer) {
            // Calculate similarity score
            $similarity = $this->calculateAnswerSimilarity(
                $targetAnswer->answer_text,
                $answer->answer_text
            );

            if ($similarity > 0.7) { // 70% similarity threshold
                $suggestions[] = [
                    'answer' => $answer,
                    'similarity' => $similarity,
                    'suggested_points' => $answer->points_awarded,
                    'feedback' => $answer->feedback,
                ];
            }
        }

        // Sort by similarity
        usort($suggestions, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        return array_slice($suggestions, 0, 3); // Top 3 suggestions
    }

    /**
     * Calculate similarity between two answers
     */
    protected function calculateAnswerSimilarity(string $answer1, string $answer2): float
    {
        if (!$answer1 || !$answer2) {
            return 0;
        }

        $answer1 = strtolower(trim($answer1));
        $answer2 = strtolower(trim($answer2));

        // Exact match
        if ($answer1 === $answer2) {
            return 1.0;
        }

        // Calculate Levenshtein distance for similarity
        $maxLen = max(strlen($answer1), strlen($answer2));
        if ($maxLen === 0) {
            return 1.0;
        }

        $distance = levenshtein($answer1, $answer2);
        return max(0, ($maxLen - $distance) / $maxLen);
    }

    /**
     * Dispatch background job for auto-grading
     */
    public function dispatchAutoGradeJob(Exam $exam): void
    {
        AutoGradeExamAttempts::dispatch($exam);
    }

    /**
     * Dispatch background job for recalculating results
     */
    public function dispatchRecalculateJob(Exam $exam): void
    {
        RecalculateExamResults::dispatch($exam);
    }
}
