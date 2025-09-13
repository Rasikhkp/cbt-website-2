<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get assigned exams
        $assignedExams = $user->assignedExams()
            ->with(['creator', 'examQuestions'])
            ->where('status', 'published')
            ->get();

        // Group exams by status
        $upcomingExams = $assignedExams->filter(function ($exam) {
            return $exam->isUpcoming();
        });

        $activeExams = $assignedExams->filter(function ($exam) {
            return $exam->isActive();
        });

        $pastExams = $assignedExams->filter(function ($exam) {
            return $exam->isPast();
        });

        return view('student.exams.index', compact('upcomingExams', 'activeExams', 'pastExams'));
    }

    public function show(Exam $exam)
    {
        $user = auth()->user();

        // Check if student is assigned to this exam
        if (!$exam->assignedStudents()->where('student_id', $user->id)->exists()) {
            abort(403, 'You are not assigned to this exam.');
        }

        // Get student's attempts for this exam
        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->orderBy('attempt_number', 'desc')
            ->get();

        // Check if there's an active attempt
        $activeAttempt = $attempts->where('status', 'in_progress')->first();

        // Check if student can start a new attempt
        $canStartNewAttempt = $attempts->count() < $exam->max_attempts;

        // Get assignment details
        $assignment = $exam->assignedStudents()->where('student_id', $user->id)->first()->pivot;

        $exam->load(['creator', 'examQuestions.question']);

        return view('student.exams.show', compact('exam', 'attempts', 'activeAttempt', 'canStartNewAttempt', 'assignment'));
    }
}
