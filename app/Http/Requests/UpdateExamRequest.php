<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamRequest extends FormRequest
{
    public function authorize()
    {
        $exam = $this->route('exam');
        return (auth()->user()->isTeacher() && $exam->created_by === auth()->id())
            || auth()->user()->isAdmin();
    }

    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'randomize_questions' => ['boolean'],
            'randomize_options' => ['boolean'],
            'allow_review' => ['boolean'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*' => ['exists:questions,id'],
            'question_marks' => ['required', 'array'],
            'question_marks.*' => ['required', 'numeric', 'min:0.1', 'max:100'],
            'students' => ['nullable', 'array'],
            'students.*' => ['exists:users,id'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'randomize_questions' => $this->has('randomize_questions'),
            'randomize_options' => $this->has('randomize_options'),
            'allow_review' => $this->has('allow_review'),
        ]);
    }
}
