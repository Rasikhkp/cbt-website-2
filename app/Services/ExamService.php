<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamService
{
    public function canStudentStartExam(Exam $exam, User $student): array
    {
        $reasons = [];
        $canStart = true;

        // Check if student is assigned
        if (!$exam->assignedStudents()->where('student_id', $student->id)->exists()) {
            $reasons[] = 'You are not assigned to this exam.';
            $canStart = false;
        }

        // Check if exam is published
        if (!$exam->isPublished()) {
            $reasons[] = 'This exam is not yet published.';
            $canStart = false;
        }

        // Check if exam is active
        if (!$exam->isActive()) {
            if ($exam->isUpcoming()) {
                $reasons[] = 'This exam has not started yet. It starts on ' . $exam->start_time->format('M j, Y g:i A');
            } elseif ($exam->isPast()) {
                $reasons[] = 'This exam has ended.';
            }
            $canStart = false;
        }

        // Check existing attempts
        $attemptCount = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->count();

        if ($attemptCount >= $exam->max_attempts) {
            $reasons[] = "You have reached the maximum number of attempts ({$exam->max_attempts}).";
            $canStart = false;
        }

        // Check for active attempt
        $activeAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if ($activeAttempt) {
            $reasons[] = 'You already have an active attempt for this exam.';
            $canStart = false;
        }

        return [
            'canStart' => $canStart,
            'reasons' => $reasons,
            'attemptCount' => $attemptCount,
            'maxAttempts' => $exam->max_attempts,
            'activeAttempt' => $activeAttempt,
        ];
    }

    public function getStudentExamSummary(User $student): array
    {
        $assignedExams = $student->assignedExams()
            ->with(['creator', 'examQuestions'])
            ->where('status', 'published')
            ->get();

        $upcoming = $assignedExams->filter(fn($exam) => $exam->isUpcoming());
        $active = $assignedExams->filter(fn($exam) => $exam->isActive());
        $past = $assignedExams->filter(fn($exam) => $exam->isPast());

        // Get attempt statistics
        $totalAttempts = ExamAttempt::where('student_id', $student->id)->count();
        $completedAttempts = ExamAttempt::where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])->count();
        $inProgressAttempts = ExamAttempt::where('student_id', $student->id)
            ->where('status', 'in_progress')->count();

        return [
            'exams' => [
                'upcoming' => $upcoming,
                'active' => $active,
                'past' => $past,
            ],
            'statistics' => [
                'totalExams' => $assignedExams->count(),
                'totalAttempts' => $totalAttempts,
                'completedAttempts' => $completedAttempts,
                'inProgressAttempts' => $inProgressAttempts,
            ],
        ];
    }

    public function calculateExamStatistics(Exam $exam): array
    {
        $totalAttempts = $exam->attempts()->count();
        $completedAttempts = $exam->attempts()->whereIn('status', ['submitted', 'graded', 'expired'])->count();
        $inProgressAttempts = $exam->attempts()->where('status', 'in_progress')->count();
        $averageScore = $exam->attempts()->whereNotNull('percentage_score')->avg('percentage_score');

        // Get score distribution
        $scoreRanges = [
            'A (90-100%)' => $exam->attempts()->whereBetween('percentage_score', [90, 100])->count(),
            'B (80-89%)' => $exam->attempts()->whereBetween('percentage_score', [80, 89.99])->count(),
            'C (70-79%)' => $exam->attempts()->whereBetween('percentage_score', [70, 79.99])->count(),
            'D (60-69%)' => $exam->attempts()->whereBetween('percentage_score', [60, 69.99])->count(),
            'F (0-59%)' => $exam->attempts()->where('percentage_score', '<', 60)->count(),
        ];

        return [
            'totalAttempts' => $totalAttempts,
            'completedAttempts' => $completedAttempts,
            'inProgressAttempts' => $inProgressAttempts,
            'averageScore' => round($averageScore ?? 0, 2),
            'scoreDistribution' => $scoreRanges,
            'completionRate' => $totalAttempts > 0 ? round(($completedAttempts / $totalAttempts) * 100, 2) : 0,
        ];
    }

    public function autoGradeAttempt(ExamAttempt $attempt): array
    {
        $results = [
            'totalQuestions' => 0,
            'autoGraded' => 0,
            'manualReviewRequired' => 0,
            'details' => [],
        ];

        $questions = $attempt->getQuestionsInOrder();
        $results['totalQuestions'] = $questions->count();

        foreach ($questions as $examQuestion) {
            $answer = $attempt->answers()
                ->where('question_id', $examQuestion->question_id)
                ->first();

            if (!$answer) {
                continue;
            }

            $question = $examQuestion->question;
            $questionResult = [
                'question_id' => $question->id,
                'type' => $question->type,
                'autoGraded' => false,
                'requiresManualReview' => false,
                'marks' => 0,
            ];

            // Auto-grade MCQ questions
            if ($question->isMCQ() && $answer->selected_options) {
                $isCorrect = $answer->isCorrectMCQ();
                $marks = $isCorrect ? $examQuestion->marks : 0;

                $answer->update([
                    'is_correct' => $isCorrect,
                    'marks_awarded' => $marks,
                    'is_graded' => true,
                    'graded_at' => now(),
                ]);

                $questionResult['autoGraded'] = true;
                $questionResult['marks'] = $marks;
                $results['autoGraded']++;
            } else {
                // Long answers and unanswered short answers need manual review
                $questionResult['requiresManualReview'] = true;
                $results['manualReviewRequired']++;
            }

            $results['details'][] = $questionResult;
        }

        // Recalculate attempt score
        $attempt->calculateScore();

        return $results;
    }

    public function getAttemptProgress(ExamAttempt $attempt): array
    {
        $questions = $attempt->getQuestionsInOrder();
        $totalQuestions = $questions->count();
        $answeredCount = 0;
        $questionProgress = [];

        foreach ($questions as $index => $examQuestion) {
            $answer = $attempt->answers()
                ->where('question_id', $examQuestion->question_id)
                ->first();

            $isAnswered = $answer && $answer->isAnswered();
            if ($isAnswered) {
                $answeredCount++;
            }

            $questionProgress[] = [
                'questionNumber' => $index + 1,
                'questionId' => $examQuestion->question_id,
                'isAnswered' => $isAnswered,
                'type' => $examQuestion->question->type,
                'marks' => $examQuestion->marks,
            ];
        }

        return [
            'totalQuestions' => $totalQuestions,
            'answeredQuestions' => $answeredCount,
            'progressPercentage' => $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100, 1) : 0,
            'questions' => $questionProgress,
        ];
    }
}
