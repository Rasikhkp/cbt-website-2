<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'assigned_at',
        'due_date',
        'is_optional',
        'special_instructions',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'due_date' => 'datetime',
        'is_optional' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
