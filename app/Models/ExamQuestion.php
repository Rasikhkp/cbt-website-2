<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'question_id',
        'question_order',
        'marks',
        'is_required',
    ];

    protected $casts = [
        'marks' => 'decimal:2',
        'is_required' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
