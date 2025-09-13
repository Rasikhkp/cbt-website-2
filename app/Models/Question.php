<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'type',
        'question_text',
        'explanation',
        'points',
        'difficulty',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'points' => 'decimal:2',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    public function images()
    {
        return $this->hasMany(QuestionImage::class)->orderBy('order');
    }

    // Helper methods
    public function isMCQ()
    {
        return $this->type === 'mcq';
    }

    public function isShort()
    {
        return $this->type === 'short';
    }

    public function isLong()
    {
        return $this->type === 'long';
    }

    public function getCorrectOptions()
    {
        return $this->options()->where('is_correct', true)->get();
    }

    public function getDifficultyColorClass()
    {
        return match ($this->difficulty) {
            'easy' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'hard' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getTypeDisplayName()
    {
        return match ($this->type) {
            'mcq' => 'Multiple Choice',
            'short' => 'Short Answer',
            'long' => 'Long Answer',
            default => 'Unknown'
        };
    }
}
