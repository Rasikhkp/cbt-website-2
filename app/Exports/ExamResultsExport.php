<?php

namespace App\Exports;

use App\Models\Exam;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExamResultsExport implements FromCollection, WithHeadings
{
    protected $exam;
    protected $attempts;

    public function __construct(Exam $exam, $attempts)
    {
        $this->exam = $exam;
        $this->attempts = $attempts;
    }

    public function collection()
    {
        $rows = [];

        $maxScore = $this->exam->total_marks;

        foreach ($this->attempts as $attempt) {
            $percentage = $maxScore > 0 ? ($attempt->total_score / $maxScore) * 100 : 0;
            $timeTaken = $attempt->started_at && $attempt->submitted_at
                ? $attempt->started_at->diff($attempt->submitted_at)
                : null;

            $timeTakenFormatted = $timeTaken
                ? ($timeTaken->i . 'm ' . $timeTaken->s . 's')
                : 'N/A';

            $row = [
                $attempt->student->name,
                $attempt->student->email,
                $attempt->attempt_number,
                $attempt->total_score ?? 'N/A',
                $maxScore,
                round($percentage, 2) . '%',
                $this->getLetterGrade($percentage),
                $timeTakenFormatted,
            ];

            // question scores
            foreach ($this->exam->questions as $question) {
                $answer = $attempt->answers->where('question_id', $question->id)->first();
                $row[] = $answer?->marks_awarded ?? 'N/A';
            }

            $rows[] = $row;
        }

        return collect($rows);
    }

    public function headings(): array
    {
        $headers = [
            'Examinee Name',
            'Examinee Email',
            'Attempt Number',
            'Score',
            'Max Score',
            'Percentage',
            'Grade',
            'Time Taken',
        ];

        foreach ($this->exam->questions as $question) {
            $headers[] = "Q" . ($question->pivot->question_order ?? 'N/A') . " Score";
        }

        return $headers;
    }

    protected function getLetterGrade($percentage): string
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 75) return 'B';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 45) return 'D';
        return 'F';
    }
}
