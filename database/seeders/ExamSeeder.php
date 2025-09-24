<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamStudent;
use App\Models\Question;
use App\Models\User;
use Carbon\Carbon;

class ExamSeeder extends Seeder
{
    public function run()
    {
        $teachers = User::where('role', 'teacher')->get();
        $students = User::where('role', 'student')->get();
        $questions = Question::all();

        if ($teachers->isEmpty() || $students->isEmpty() || $questions->isEmpty()) {
            return; // Skip if no data available
        }

        $exams = [
            [
                'title' => 'Mathematics Midterm Exam',
                'description' => 'Comprehensive exam covering algebra, geometry, and basic calculus',
                'instructions' => 'Read each question carefully. Show your work for calculation problems. You have 90 minutes to complete this exam.',
                'start_time' => Carbon::now()->addDays(7),
                'end_time' => Carbon::now()->addDays(7)->addHours(3),
                'duration_minutes' => 90,
                'randomize_questions' => true,
                'randomize_options' => true,
                'allow_review' => true,
                'max_attempts' => 1,
                'status' => 'published',
            ],
            [
                'title' => 'Science Quiz - Chemistry & Physics',
                'description' => 'Quick quiz on basic chemistry and physics concepts',
                'instructions' => 'Multiple choice questions only. Select the best answer for each question.',
                'start_time' => Carbon::now()->addDays(3),
                'end_time' => Carbon::now()->addDays(3)->addHours(2),
                'duration_minutes' => 30,
                'randomize_questions' => false,
                'randomize_options' => true,
                'allow_review' => true,
                'max_attempts' => 2,
                'status' => 'published',
            ],
            [
                'title' => 'Computer Science Fundamentals',
                'description' => 'Exam covering data structures, algorithms, and programming concepts',
                'instructions' => 'Mix of multiple choice and short answer questions. Pay attention to time management.',
                'start_time' => Carbon::now()->addDays(10),
                'end_time' => Carbon::now()->addDays(10)->addHours(4),
                'duration_minutes' => 120,
                'randomize_questions' => true,
                'randomize_options' => false,
                'allow_review' => true,
                'max_attempts' => 1,
                'status' => 'draft',
            ]
        ];

        foreach ($exams as $examData) {
            $teacher = $teachers->random();

            $exam = Exam::create([
                'created_by' => $teacher->id,
                'title' => $examData['title'],
                'description' => $examData['description'],
                'instructions' => $examData['instructions'],
                'start_time' => $examData['start_time'],
                'end_time' => $examData['end_time'],
                'duration_minutes' => $examData['duration_minutes'],
                'randomize_questions' => $examData['randomize_questions'],
                'randomize_options' => $examData['randomize_options'],
                'allow_review' => $examData['allow_review'],
                'max_attempts' => $examData['max_attempts'],
                'status' => $examData['status'],
            ]);

            // Add random questions to exam (5-10 questions per exam)
            $examQuestions = $questions->random(rand(5, 10));
            $totalMarks = 0;

            foreach ($examQuestions as $index => $question) {
                $marks = $question->points + (rand(0, 2) * 0.5); // Vary marks slightly
                $totalMarks += $marks;

                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_id' => $question->id,
                    'question_order' => $index + 1,
                    'marks' => $marks,
                    'is_required' => true,
                ]);
            }

            // Update total marks
            $exam->update(['total_marks' => $totalMarks]);

            // Assign random students to published exams
            if ($exam->status === 'published') {
                $assignedStudents = $students->random(rand(3, min(8, $students->count())));

                foreach ($assignedStudents as $student) {
                    ExamStudent::create([
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                        'assigned_at' => Carbon::now(),
                        'due_date' => $exam->end_time,
                        'is_optional' => false,
                    ]);
                }
            }
        }
    }
}
