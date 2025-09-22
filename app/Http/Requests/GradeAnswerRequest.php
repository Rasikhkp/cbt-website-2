<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeAnswerRequest extends FormRequest
{
    public function rules()
    {
        return [
            'marks_awarded' => ['nullable', 'numeric', 'min:0'],
            'grader_comments' => ['nullable', 'string', 'max:2000'],
            'is_correct' => ['nullable', 'boolean'],
        ];
    }
}
