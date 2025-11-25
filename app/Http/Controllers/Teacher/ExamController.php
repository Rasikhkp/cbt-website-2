<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use App\Models\ExamQuestion;
use App\Models\ExamStudent;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    public function index(Request $request)
    {
        $query = Exam::with(['creator', 'examQuestions', 'assignedStudents']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('teacher.exams.index', compact('exams'));
    }

    public function create()
    {
        $questions = Question::with(['options', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $students = User::where('role', 'student')->orderBy('name')->get();
        $uniqueTags = $this->examService->getUniqueTagsForSelect();

        return view('teacher.exams.create', compact('questions', 'students', 'uniqueTags'));
    }

    public function store(StoreExamRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create exam
            $exam = Exam::create([
                'created_by' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration_minutes' => $request->duration_minutes,
                'randomize_questions' => $request->randomize_questions,
                'randomize_options' => $request->randomize_options,
                'max_attempts' => $request->max_attempts,
            ]);

            // Add questions to exam
            $totalMarks = 0;
            foreach ($request->questions as $index => $questionId) {
                $marks = $request->question_marks[$index] ?? 1;
                $totalMarks += $marks;

                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_id' => $questionId,
                    'question_order' => $index + 1,
                    'marks' => $marks,
                ]);
            }

            // Update total marks
            $exam->update(['total_marks' => $totalMarks]);

            // Assign students if provided
            if ($request->filled('students')) {
                foreach ($request->students as $studentId) {
                    ExamStudent::create([
                        'exam_id' => $exam->id,
                        'student_id' => $studentId,
                        'assigned_at' => now(),
                        'due_date' => $exam->end_time,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('teacher.exams.index')
                ->with('success', 'Exam created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Exam $exam)
    {
        $exam->load(['examQuestions.question.options', 'assignedStudents', 'creator']);
        return view('teacher.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        // Don't allow editing published exams that have started
        if ($exam->isPublished() && now()->gte($exam->start_time)) {
            return redirect()->route('teacher.exams.show', $exam)
                ->with('error', 'Cannot edit an exam that has already started.');
        }

        $exam->load(['examQuestions.question', 'assignedStudents']);

        $questions = Question::with(['options', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $students = User::where('role', 'student')->orderBy('name')->get();
        $uniqueTags = $this->examService->getUniqueTagsForSelect();

        return view('teacher.exams.edit', compact('exam', 'questions', 'students', 'uniqueTags'));
    }

    public function update(UpdateExamRequest $request, Exam $exam)
    {
        // Don't allow updating published exams that have started
        if ($exam->isPublished() && now()->gte($exam->start_time)) {
            return back()->with('error', 'Cannot update an exam that has already started.');
        }

        try {
            DB::beginTransaction();

            // Update exam
            $exam->update([
                'title' => $request->title,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration_minutes' => $request->duration_minutes,
                'randomize_questions' => $request->randomize_questions,
                'randomize_options' => $request->randomize_options,
                'max_attempts' => $request->max_attempts,
            ]);

            // Update questions
            $exam->examQuestions()->delete();

            $totalMarks = 0;
            foreach ($request->questions as $index => $questionId) {
                $marks = $request->question_marks[$index] ?? 1;
                $totalMarks += $marks;

                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_id' => $questionId,
                    'question_order' => $index + 1,
                    'marks' => $marks,
                ]);
            }

            // Update total marks
            $exam->update(['total_marks' => $totalMarks]);

            // Update student assignments
            $exam->examStudents()->delete();

            if ($request->filled('students')) {
                foreach ($request->students as $studentId) {
                    ExamStudent::create([
                        'exam_id' => $exam->id,
                        'student_id' => $studentId,
                        'assigned_at' => now(),
                        'due_date' => $exam->end_time,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('teacher.exams.index')
                ->with('success', 'Exam updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Exam $exam)
    {
        try {
            $exam->delete();
            return redirect()->route('teacher.exams.index')
                ->with('success', 'Exam deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting exam: ' . $e->getMessage());
        }
    }

    public function publish(Exam $exam)
    {
        if ($exam->examQuestions()->count() === 0) {
            return back()->with('error', 'Cannot publish an exam with no questions.');
        }

        if ($exam->assignedStudents()->count() === 0) {
            return back()->with('error', 'Cannot publish an exam with no assigned students.');
        }

        $exam->update(['status' => 'published']);

        return back()->with('success', 'Exam published successfully.');
    }

    public function unpublish(Exam $exam)
    {
        // Don't allow unpublishing exams that have started
        if (now()->gte($exam->start_time)) {
            return back()->with('error', 'Cannot unpublish an exam that has already started.');
        }

        $exam->update(['status' => 'draft']);

        return back()->with('success', 'Exam unpublished successfully.');
    }
}
