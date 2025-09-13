<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer_text',
        'selected_options',
        'marks_awarded',
        'is_correct',
        'is_graded',
        'grader_comments',
        'graded_by',
        'graded_at',
        'answered_at',
        'answer_metadata',
    ];

    protected $casts = [
        'selected_options' => 'array',
        'marks_awarded' => 'decimal:2',
        'is_correct' => 'boolean',
        'is_graded' => 'boolean',
        'graded_at' => 'datetime',
        'answered_at' => 'datetime',
        'answer_metadata' => 'array',
    ];

    // Relationships
    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // Helper methods
    public function isAnswered()
    {
        return !empty($this->answer_text) || !empty($this->selected_options);
    }

    public function getSelectedOptionTexts()
    {
        if (!$this->selected_options || !$this->question) {
            return [];
        }

        return $this->question->options()
            ->whereIn('id', $this->selected_options)
            ->pluck('option_text')
            ->toArray();
    }

    public function isCorrectMCQ()
    {
        if (!$this->question->isMCQ() || !$this->selected_options) {
            return null;
        }

        $correctOptionIds = $this->question->options()
            ->where('is_correct', true)
            ->pluck('id')
            ->sort()
            ->values()
            ->toArray();

        $selectedOptionIds = collect($this->selected_options)
            ->sort()
            ->values()
            ->toArray();

        return $correctOptionIds === $selectedOptionIds;
    }
}
