<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'attempt_number',
        'started_at',
        'submitted_at',
        'expires_at',
        'time_remaining_seconds',
        'total_score',
        'percentage_score',
        'status',
        'question_order',
        'suspicious_behaviour',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'expires_at' => 'datetime',
        'question_order' => 'array',
        'session_data' => 'array',
        'total_score' => 'decimal:2',
        'percentage_score' => 'decimal:2',
    ];

    public $incrementing = false; // disable auto-increment
    protected $keyType = 'string'; // store UUID as string

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }

    // Helper methods
    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted()
    {
        return in_array($this->status, ['submitted', 'graded']);
    }

    public function isExamEnded()
    {
        return now()->gt($this->exam->end_time);
    }

    public function isGraded()
    {
        return $this->status === 'graded';
    }

    public function getRemainingTimeSeconds()
    {
        if ($this->isSubmitted()) {
            return 0;
        }

        // Otherwise calculate from expires_at
        $remaining = now()->diffInSeconds($this->expires_at, false);
        return max(0, $remaining);
    }

    public function getRemainingTimeFormatted()
    {
        $seconds = $this->getRemainingTimeSeconds();
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getQuestionsInOrder()
    {
        $exam = $this->exam;
        $examQuestions = $exam->examQuestions()->with(['question.options', 'question.images'])->get();

        // If question order is randomized and stored, use it
        if ($this->question_order && count($this->question_order) > 0) {
            $orderedQuestions = collect();
            foreach ($this->question_order as $questionId) {
                $examQuestion = $examQuestions->where('question_id', $questionId)->first();
                if ($examQuestion) {
                    $orderedQuestions->push($examQuestion);
                }
            }
            return $orderedQuestions;
        }

        // Otherwise use default order
        return $examQuestions;
    }

    public function calculateScore()
    {
        $totalMarks = $this->exam->total_marks;
        $scoredMarks = $this->answers()
            ->whereNotNull('marks_awarded')
            ->sum('marks_awarded');

        $this->update([
            'total_score' => $scoredMarks,
            'percentage_score' => $totalMarks > 0 ? ($scoredMarks / $totalMarks) * 100 : 0,
        ]);

        return $scoredMarks;
    }

    public function autoGrade()
    {
        $questions = $this->getQuestionsInOrder();
        $totalAutoGraded = 0;

        foreach ($questions as $examQuestion) {
            $answer = $this->answers()
                ->where('question_id', $examQuestion->question_id)
                ->first();

            if (!$answer || $answer->is_graded) {
                continue; // Skip if no answer or already graded
            }

            $question = $examQuestion->question;
            $isCorrect = false;
            $marksAwarded = 0;

            // Auto-grade MCQ questions
            if ($question->isMCQ() && $answer->selected_options) {
                $correctOptionIds = $question->options()
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->map(fn($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->toArray();

                $selectedOptionIds = collect($answer->selected_options)
                    ->map(fn($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->toArray();

                $isCorrect = $correctOptionIds === $selectedOptionIds;
                $marksAwarded = $isCorrect ? $examQuestion->marks : 0;
            }

            // Update answer with auto-grading results
            if ($question->isMCQ()) {
                $answer->update([
                    'is_correct' => $isCorrect,
                    'marks_awarded' => $marksAwarded,
                    'is_graded' => true,
                    'graded_at' => now(),
                ]);

                $totalAutoGraded++;
            }
        }

        // Update overall attempt score
        $this->calculateScore();

        // Mark as graded if all questions are auto-gradeable and graded
        $totalQuestions = $questions->count();
        $gradedAnswers = $this->answers()->where('is_graded', true)->count();

        if ($gradedAnswers === $totalQuestions) {
            $this->update(['status' => 'graded']);
        }

        return $totalAutoGraded;
    }

    public function autoSubmit()
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->autoGrade();
    }

    public function getProgressPercentage()
    {
        $totalQuestions = $this->exam->examQuestions()->count();
        if ($totalQuestions === 0) return 0;

        $answeredQuestions = $this->answers()
            ->where(function ($query) {
                $query->whereNotNull('answer_text')
                    ->orWhereNotNull('selected_options');
            })
            ->count();

        return round(($answeredQuestions / $totalQuestions) * 100, 1);
    }
}
