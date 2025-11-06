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
            'new_images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'],
            'existing_images.*' => ['nullable', 'string'],
        ];

        // MCQ specific validation
        if ($this->type === 'mcq') {
            $rules['options'] = ['required', 'array', 'min:2', 'max:6'];
            $rules['options.*.option_text'] = ['required', 'string', 'min:1'];
            $rules['options.*.new_option_image'] = ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'];
            $rules['options.*.existing_option_image'] = ['nullable', 'string',];
            $rules['correct_options'] = ['required', 'array', 'min:1'];
            $rules['correct_options.*'] = ['integer', 'min:0'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'correct_options.required' => 'Please select at least one correct answer.',
            'options.*.option_text.required' => 'MCQ option text cannot be empty',
            'options.*.new_option_image.max' => 'Each image must be smaller than 2MB.',
            'options.min' => 'MCQ questions must have at least 2 options.',
            'options.max' => 'MCQ questions can have maximum 6 options.',
            'new_images.*.max' => 'Each image must be smaller than 2MB.',
        ];
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
