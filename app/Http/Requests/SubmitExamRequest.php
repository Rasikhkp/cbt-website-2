<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitExamRequest extends FormRequest
{
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
