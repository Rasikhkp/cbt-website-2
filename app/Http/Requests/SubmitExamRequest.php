<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitExamRequest extends FormRequest
{
    public function authorize()
    {
        $attempt = $this->route('attempt');
        $user = auth()->user();

        // Must be the student who owns this attempt
        if ($attempt->student_id !== $user->id) {
            return false;
        }

        // Attempt must be in progress (can submit expired attempts)
        return $attempt->isInProgress() || $attempt->isExpired();
    }

    public function rules()
    {
        return [
            'confirm_submit' => 'required|accepted',
        ];
    }

    public function messages()
    {
        return [
            'confirm_submit.accepted' => 'You must confirm that you want to submit the exam.',
        ];
    }
}
