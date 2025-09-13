<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Exam;
use App\Models\ExamAttempt;

class StartExamRequest extends FormRequest
{
    public function authorize()
    {
        $exam = $this->route('exam');
        $user = auth()->user();

        // Must be a student
        if (!$user->isStudent()) {
            return false;
        }

        // Must be assigned to this exam
        $isAssigned = $exam->assignedStudents()->where('student_id', $user->id)->exists();
        if (!$isAssigned) {
            return false;
        }

        // Exam must be published and active
        if (!$exam->isPublished() || !$exam->isActive()) {
            return false;
        }

        // Check attempt limits
        $attemptCount = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->count();

        return $attemptCount < $exam->max_attempts;
    }

    public function rules()
    {
        return [
            'confirm_start' => 'required|accepted',
        ];
    }

    public function messages()
    {
        return [
            'confirm_start.accepted' => 'You must confirm that you want to start the exam.',
        ];
    }
}
