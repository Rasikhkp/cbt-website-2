<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResultService
{
    protected $gradingService;

    public function __construct(GradingService $gradingService)
    {
        $this->gradingService = $gradingService;
    }

    /**
     * Get comprehensive statistics for an exam
     */
    public function getExamStatistics(Exam $exam): array
    {
        $attempts = $exam->attempts()
            ->where('status', 'graded')
            ->get();

        if ($attempts->isEmpty()) {
            return [
                'total_attempts' => 0,
                'average_score' => 'N/A',
                'highest_score' => 'N/A',
                'lowest_score' => 'N/A',
                'median_score' => 'N/A',
                'standard_deviation' => 'N/A',
                'grade_distribution' => [],
                'max_possible_score' => $this->getMaxPossibleScore($exam),
            ];
        }

        $scores = $attempts->pluck('total_score')->toArray();
        $maxPossibleScore = $this->getMaxPossibleScore($exam);

        return [
            'total_attempts' => $attempts->count(),
            'average_score' => round(array_sum($scores) / count($scores), 2),
            'highest_score' => max($scores),
            'lowest_score' => min($scores),
            'median_score' => $this->calculateMedian($scores),
            'standard_deviation' => $this->calculateStandardDeviation($scores),
            'grade_distribution' => $this->calculateGradeDistribution($scores, $maxPossibleScore),
            'max_possible_score' => $maxPossibleScore,
            'pass_rate' => $this->calculatePassRate($scores, $maxPossibleScore),
            'percentile_25' => $this->calculatePercentile($scores, 25),
            'percentile_75' => $this->calculatePercentile($scores, 75),
        ];
    }

    /**
     * Get maximum possible score for an exam
     */
    protected function getMaxPossibleScore(Exam $exam): float
    {
        return DB::table('exam_questions')
            ->where('exam_id', $exam->id)
            ->sum('marks') ?? 0;
    }

    /**
     * Calculate median of an array
     */
    protected function calculateMedian(array $scores): float
    {
        sort($scores);
        $count = count($scores);

        if ($count === 0) {
            return 0;
        }

        $middle = floor($count / 2);

        if ($count % 2 === 0) {
            return ($scores[$middle - 1] + $scores[$middle]) / 2;
        } else {
            return $scores[$middle];
        }
    }

    /**
     * Calculate standard deviation
     */
    protected function calculateStandardDeviation(array $scores): float
    {
        if (count($scores) < 2) {
            return 0;
        }

        $mean = array_sum($scores) / count($scores);
        $squaredDifferences = array_map(function ($score) use ($mean) {
            return pow($score - $mean, 2);
        }, $scores);

        $variance = array_sum($squaredDifferences) / count($scores);
        return round(sqrt($variance), 2);
    }

    /**
     * Calculate grade distribution
     */
    protected function calculateGradeDistribution(array $scores, float $maxScore): array
    {
        $distribution = [
            'A (90-100%)' => 0,
            'B (80-89%)' => 0,
            'C (70-79%)' => 0,
            'D (60-69%)' => 0,
            'F (0-59%)' => 0,
        ];

        foreach ($scores as $score) {
            $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;

            if ($percentage >= 90) {
                $distribution['A (90-100%)']++;
            } elseif ($percentage >= 80) {
                $distribution['B (80-89%)']++;
            } elseif ($percentage >= 70) {
                $distribution['C (70-79%)']++;
            } elseif ($percentage >= 60) {
                $distribution['D (60-69%)']++;
            } else {
                $distribution['F (0-59%)']++;
            }
        }

        return $distribution;
    }

    /**
     * Calculate pass rate (60% or above)
     */
    protected function calculatePassRate(array $scores, float $maxScore): float
    {
        if (empty($scores) || $maxScore <= 0) {
            return 0;
        }

        $passingCount = 0;
        foreach ($scores as $score) {
            $percentage = ($score / $maxScore) * 100;
            if ($percentage >= 60) {
                $passingCount++;
            }
        }

        return round(($passingCount / count($scores)) * 100, 1);
    }

    /**
     * Calculate percentile
     */
    protected function calculatePercentile(array $scores, int $percentile): float
    {
        if (empty($scores)) {
            return 0;
        }

        sort($scores);
        $index = ($percentile / 100) * (count($scores) - 1);

        if (floor($index) === $index) {
            return $scores[$index];
        } else {
            $lower = $scores[floor($index)];
            $upper = $scores[ceil($index)];
            return $lower + (($upper - $lower) * ($index - floor($index)));
        }
    }

    /**
     * Get grade distribution data for charts
     */
    public function getGradeDistribution(Exam $exam): array
    {
        $statistics = $this->getExamStatistics($exam);
        return $statistics['grade_distribution'];
    }

    /**
     * Export results in various formats
     */
    public function exportResults(Exam $exam, string $format = 'csv'): StreamedResponse
    {
        $attempts = $exam->attempts()
            ->where('status', 'graded')
            ->with(['student', 'answers.question'])
            ->orderBy('total_score', 'desc')
            ->get();

        $filename = "exam_results_{$exam->id}_" . now()->toDateString() . ".{$format}";

        return response()->streamDownload(function () use ($attempts, $exam, $format) {
            $handle = fopen('php://output', 'w');

            $method = $format === 'csv' ? 'exportToCsv' : 'exportToExcel';
            $this->{$method}($handle, $attempts, $exam);

            fclose($handle);
        }, $filename, [
            'Content-Type' => $format === 'csv'
                ? 'text/csv'
                : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Export to CSV format
     */
    protected function exportToCsv($handle, $attempts, Exam $exam): void
    {
        // Write header
        $headers = [
            'Student Name',
            'Email',
            'Total Score',
            'Max Score',
            'Percentage',
            'Grade',
            'Submitted At',
            'Time Taken (minutes)',
        ];

        // Add question headers
        $questions = $exam->questions()->orderBy('exam_questions.order')->get();
        foreach ($questions as $question) {
            $headers[] = "Q" . ($question->pivot->order ?? 'N/A') . " Score";
        }

        fputcsv($handle, $headers);

        $maxScore = $this->getMaxPossibleScore($exam);

        // Write data rows
        foreach ($attempts as $attempt) {
            $percentage = $maxScore > 0 ? ($attempt->total_score / $maxScore) * 100 : 0;
            $letterGrade = $this->getLetterGrade($percentage);
            $timeTaken = $attempt->started_at && $attempt->submitted_at
                ? $attempt->started_at->diffInMinutes($attempt->submitted_at)
                : 'N/A';

            $row = [
                $attempt->student->name,
                $attempt->student->email,
                $attempt->total_score ?? 'N/A',
                $maxScore,
                round($percentage, 2) . '%',
                $letterGrade,
                $attempt->submitted_at?->format('Y-m-d H:i:s') ?? 'N/A',
                $timeTaken,
            ];

            // Add question scores
            foreach ($questions as $question) {
                $answer = $attempt->answers->where('question_id', $question->id)->first();
                $row[] = $answer?->points_awarded ?? 'N/A';
            }

            fputcsv($handle, $row);
        }
    }

    /**
     * Export to Excel format (basic CSV for now, can be enhanced)
     */
    protected function exportToExcel($handle, $attempts, Exam $exam): void
    {
        // For now, use CSV format. This can be enhanced with a proper Excel library
        $this->exportToCsv($handle, $attempts, $exam);
    }

    /**
     * Get letter grade from percentage
     */
    protected function getLetterGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    /**
     * Recalculate all scores for an exam
     */
    public function recalculateAllScores(Exam $exam): int
    {
        $attempts = $exam->attempts()
            ->where('status', 'graded')
            ->get();

        $updatedCount = 0;

        foreach ($attempts as $attempt) {
            $this->gradingService->recalculateAttemptScore($attempt);
            $updatedCount++;
        }

        return $updatedCount;
    }

    /**
     * Generate summary report
     */
    public function generateSummaryReport(Exam $exam): array
    {
        $statistics = $this->getExamStatistics($exam);
        $gradingStats = $this->gradingService->getGradingStatistics($exam);

        return [
            'exam_info' => [
                'title' => $exam->title,
                'created_at' => $exam->created_at->format('Y-m-d H:i'),
                'duration' => $exam->duration ?? 'No limit',
                'total_questions' => $exam->questions()->count(),
                'max_score' => $statistics['max_possible_score'],
            ],
            'participation' => [
                'total_attempts' => $statistics['total_attempts'],
                'completion_rate' => $this->calculateCompletionRate($exam),
            ],
            'performance' => [
                'average_score' => $statistics['average_score'],
                'highest_score' => $statistics['highest_score'],
                'lowest_score' => $statistics['lowest_score'],
                'pass_rate' => $statistics['pass_rate'] ?? 0,
                'standard_deviation' => $statistics['standard_deviation'],
            ],
            'grading_status' => [
                'total_answers' => $gradingStats['total_answers'],
                'graded_answers' => $gradingStats['graded_answers'],
                'grading_progress' => $gradingStats['grading_progress'],
                'auto_graded' => $gradingStats['auto_graded_answers'],
                'manual_graded' => $gradingStats['manual_graded_answers'],
            ],
            'grade_distribution' => $statistics['grade_distribution'],
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Calculate completion rate
     */
    protected function calculateCompletionRate(Exam $exam): float
    {
        $totalAttempts = $exam->attempts()->count();
        $completedAttempts = $exam->attempts()->where('status', 'graded')->count();

        return $totalAttempts > 0 ? round(($completedAttempts / $totalAttempts) * 100, 1) : 0;
    }

    /**
     * Get detailed student performance data
     */
    public function getStudentPerformanceData(Exam $exam, User $student): array
    {
        $attempt = $exam->attempts()
            ->where('student_id', $student->id)
            ->where('status', 'completed')
            ->with(['answers.question'])
            ->first();

        if (!$attempt) {
            return ['error' => 'No completed attempt found for this student'];
        }

        $statistics = $this->getExamStatistics($exam);
        $maxScore = $statistics['max_possible_score'];
        $studentPercentage = $maxScore > 0 ? ($attempt->total_score / $maxScore) * 100 : 0;

        return [
            'attempt_info' => [
                'submitted_at' => $attempt->submitted_at,
                'time_taken' => $attempt->time_taken,
                'total_score' => $attempt->total_score,
                'max_score' => $maxScore,
                'percentage' => round($studentPercentage, 2),
                'letter_grade' => $this->getLetterGrade($studentPercentage),
            ],
            'class_comparison' => [
                'class_average' => $statistics['average_score'],
                'percentile_rank' => $this->calculateStudentPercentile($attempt->total_score, $exam),
                'above_average' => $attempt->total_score > $statistics['average_score'],
            ],
            'question_breakdown' => $this->getQuestionBreakdown($attempt),
        ];
    }

    /**
     * Calculate student's percentile rank
     */
    protected function calculateStudentPercentile(float $studentScore, Exam $exam): float
    {
        $allScores = $exam->attempts()
            ->where('status', 'completed')
            ->whereNotNull('total_score')
            ->pluck('total_score')
            ->toArray();

        if (empty($allScores)) {
            return 0;
        }

        $belowCount = count(array_filter($allScores, function ($score) use ($studentScore) {
            return $score < $studentScore;
        }));

        return round(($belowCount / count($allScores)) * 100, 1);
    }

    /**
     * Get question-by-question breakdown for an attempt
     */
    protected function getQuestionBreakdown(ExamAttempt $attempt): array
    {
        $breakdown = [];

        foreach ($attempt->answers as $answer) {
            $maxPoints = $this->gradingService->getMaxPointsForQuestion(
                $answer->question,
                $attempt->exam_id
            );

            $breakdown[] = [
                'question_id' => $answer->question_id,
                'question_text' => $answer->question->question_text,
                'question_type' => $answer->question->type,
                'student_answer' => $answer->answer_text,
                'points_awarded' => $answer->points_awarded,
                'max_points' => $maxPoints,
                'percentage' => $maxPoints > 0 ? round(($answer->points_awarded / $maxPoints) * 100, 1) : 0,
                'feedback' => $answer->feedback,
                'is_correct' => $answer->points_awarded == $maxPoints,
            ];
        }

        return $breakdown;
    }
}
