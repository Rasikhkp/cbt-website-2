<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class QuestionImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $tags = explode(',', $row['tags']);
        $tags = array_map('trim', $tags);

        $question = Question::create([
            "created_by" => Auth::id(),
            "type" => $row['type'],
            "question_text" => $row['question_text'],
            "explanation" => $row["explanation"],
            "points" => $row["points"],
            "difficulty" => $row["difficulty"],
            "tags" => $tags
        ]);

        if ($row['type'] === 'mcq') {
            $options = [
                'a' => $row['option_a'],
                'b' => $row['option_b'],
                'c' => $row['option_c'],
                'd' => $row['option_d'],
                'e' => $row['option_e'],
                'f' => $row['option_f'],
            ];

            $correctOption = strtolower($row['correct_option']);

            $i = 0;
            foreach ($options as $key => $value) {
                if (!empty($value)) {
                    $question->options()->create([
                        "question_id" => $question->id,
                        "option_text" => $value,
                        "is_correct" => $correctOption === $key,
                        "order" => $i
                    ]);

                    $i++;
                }
            }
        }

        return $question;
    }

    public function rules(): array
    {
        return [
            '*.type'  => ['required', 'in:mcq,short,long'],
            '*.question_text' => ['required', 'string'],
            '*.explanation' => [
                'nullable',
                'required_if:*.type,short,long',
                'string',
            ],
            '*.points' => ['required', 'numeric'],
            '*.difficulty' => ['nullable', 'in:easy,medium,hard'],
            '*.tags' => ['nullable', 'string'],
            '*.option_a' => ['nullable'],
            '*.option_b' => ['nullable'],
            '*.option_c' => ['nullable'],
            '*.option_d' => ['nullable'],
            '*.option_e' => ['nullable'],
            '*.option_f' => ['nullable'],
            '*.correct_option' => ['nullable', 'in:a,b,c,d,e,f'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            // --- Type ---
            '*.type.required' => 'Question type is required.',
            '*.type.in' => 'Question type must be one of: mcq, short, or long.',

            // --- Question Text ---
            '*.question_text.required' => 'Question text is required.',
            '*.question_text.string' => 'Question text must be a valid string.',

            // --- Explanation ---
            '*.explanation.required_if' => 'Explanation is required for short and long questions.',
            '*.explanation.string' => 'Explanation must be a valid string.',

            // --- Points ---
            '*.points.required' => 'Points value is required.',
            '*.points.numeric' => 'Points must be a numeric value.',

            // --- Difficulty ---
            '*.difficulty.in' => 'Difficulty must be one of: easy, medium, or hard.',

            // --- Tags ---
            '*.tags.string' => 'Tags must be a valid string.',

            // --- Options ---
            '*.option_a.string' => 'Option A must be a valid string.',
            '*.option_b.string' => 'Option B must be a valid string.',
            '*.option_c.string' => 'Option C must be a valid string.',
            '*.option_d.string' => 'Option D must be a valid string.',
            '*.option_e.string' => 'Option E must be a valid string.',
            '*.option_f.string' => 'Option F must be a valid string.',

            // --- Correct Option ---
            '*.correct_option.in' => 'Correct option must be one of: a, b, c, d, e, or f.',
        ];
    }
}
