<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'instructions',
        'start_time',
        'end_time',
        'duration_minutes',
        'total_marks',
        'randomize_questions',
        'randomize_options',
        'max_attempts',
        'status',
        'settings',
        'results_released',
        'results_released_at'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'randomize_questions' => 'boolean',
        'randomize_options' => 'boolean',
        'total_marks' => 'integer',
        'settings' => 'array',
        'results_released' => 'boolean',
        'results_released_at' => 'datetime'
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
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
            ->withPivot(['question_order', 'marks', 'is_required'])
            ->orderBy('exam_questions.question_order');
    }

    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('question_order');
    }

    public function assignedStudents()
    {
        return $this->belongsToMany(User::class, 'exam_students', 'exam_id', 'student_id')
            ->withPivot(['assigned_at', 'due_date', 'is_optional', 'special_instructions']);
    }

    public function examStudents()
    {
        return $this->hasMany(ExamStudent::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function getActiveAttempts()
    {
        return $this->attempts()->where('status', 'in_progress')->get();
    }

    public function getCompletedAttempts()
    {
        return $this->attempts()->whereIn('status', ['submitted', 'graded'])->get();
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isActive()
    {
        $now = now();
        return $this->isPublished() &&
            $now->between($this->start_time, $this->end_time);
    }

    public function isUpcoming()
    {
        return $this->isPublished() && now()->lt($this->start_time);
    }

    public function isPast()
    {
        return now()->gt($this->end_time);
    }

    public function getStatusColorClass()
    {
        return match ($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'published' => 'bg-green-100 text-green-800',
            'completed' => 'bg-blue-100 text-blue-800',
            'archived' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getDurationFormatted()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
        }

        return $minutes . 'm';
    }

    public function calculateTotalMarks()
    {
        return $this->examQuestions()->sum('marks');
    }

    public function updateTotalMarks()
    {
        $this->update(['total_marks' => $this->calculateTotalMarks()]);
    }
}
