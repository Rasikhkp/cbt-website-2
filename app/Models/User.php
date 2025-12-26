<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Role helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'created_by');
    }

    public function createdExams()
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    public function assignedExams()
    {
        return $this->belongsToMany(Exam::class, 'exam_students', 'student_id', 'exam_id')
            ->withPivot(['assigned_at', 'due_date', 'is_optional', 'special_instructions']);
    }

    public function examAssignments()
    {
        return $this->hasMany(ExamStudent::class, 'student_id');
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class, 'student_id');
    }

    public function examAnswers()
    {
        return $this->hasManyThrough(ExamAnswer::class, ExamAttempt::class, 'student_id', 'attempt_id');
    }

    public function gradedAnswers()
    {
        return $this->hasMany(ExamAnswer::class, 'graded_by');
    }
}
