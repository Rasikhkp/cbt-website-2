<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class SubmitAnswerRequest extends FormRequest
{
    public function authorize()
    {
        $attempt = $this->route('attempt');
        $user = auth()->user();

        // Must be the student who owns this attempt
        if ($attempt->student_id !== $user->id) {
            return false;
        }

        return true;
    }

    public function rules()
    {
        $question = $this->route('question');

        $rules = [
            'question_id' => 'required|exists:questions,id',
        ];

        // Validation based on question type
        if ($question && $question->isMCQ()) {
            $rules['selected_options'] = 'nullable|array';
            $rules['selected_options.*'] = 'exists:question_options,id';
        } else {
            $rules['answer_text'] = 'nullable|string|max:10000';
        }

        return $rules;
    }
}
