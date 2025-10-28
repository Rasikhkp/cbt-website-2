<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize()
    {
        $question = $this->route('question');
        return (auth()->user()->isTeacher() && $question->created_by === auth()->id())
            || auth()->user()->isAdmin();
    }

    public function rules()
    {
        $rules = [
            'type' => ['required', 'in:mcq,short,long'],
            'question_text' => ['required', 'string', 'min:10'],
            'explanation' => ['nullable', 'string'],
            'points' => ['required', 'numeric', 'min:0.1', 'max:100'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
        ];

        // MCQ specific validation
        if ($this->type === 'mcq') {
            $rules['options'] = ['required', 'array', 'min:2', 'max:6'];
            $rules['options.*'] = ['required', 'string', 'min:1'];
            $rules['correct_options'] = ['required', 'array', 'min:1'];
            $rules['correct_options.*'] = ['integer', 'min:0'];
        }

        return $rules;
    }

    public function prepareForValidation()
    {
        if ($this->tags) {
            $this->merge([
                'tags' => array_map('trim', explode(',', $this->tags))
            ]);
        }
    }
}
