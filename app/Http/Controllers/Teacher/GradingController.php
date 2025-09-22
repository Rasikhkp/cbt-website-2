<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeAnswerRequest;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\User;
use App\Models\ExamQuestion;
use App\Models\ExamStudent;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GradingController extends Controller
{
    protected $gradingService;

    public function __construct(GradingService $gradingService)
    {
        $this->gradingService = $gradingService;
    }

    /**
     * Show list of exams that need grading
     */
    public function index(Request $request)
    {
        $examsNeedingGrading = Exam::where('created_by', auth()->id())
            ->whereHas('attempts', function ($query) {
                $query->where('status', 'submitted');
            })
            ->get();

        return view('teacher.grading.index', compact('examsNeedingGrading'));
    }

    /**
     * Show grading overview for specific exam
     */
    public function exam(Exam $exam)
    {
        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->with([
                'student',
                'answers' => function ($query) {
                    $query->with('question');
                }
            ])
            ->withCount([
                'answers as total_answers',
                'answers as graded_answers' => function ($query) {
                    $query->where('is_graded', true);
                },
                'answers as ungraded_answers' => function ($query) {
                    $query->where('is_graded', false);
                }
            ])
            ->get();

        return view('teacher.grading.exam', compact('exam', 'attempts'));
    }

    public function attempt(ExamAttempt $attempt)
    {
        $attempt->load([
            'student',
            'exam',
            'answers' => function ($query) {
                $query->with(['question', 'question.options']);
            }
        ]);

        return view('teacher.results.review', compact('attempt'));
    }

    /**
     * Grade individual answer
     */
    public function gradeAnswer(GradeAnswerRequest $request, ExamAnswer $answer)
    {
        $validated = $request->validated();

        $marksAwarded = $answer->question->type === "mcq" ? ($validated['is_correct'] ? $answer->question->points : '0') : $validated["marks_awarded"];

        $answer->update([
            'marks_awarded' => $marksAwarded,
            'grader_comments' => $validated['grader_comments'],
            'is_graded' => true,
            'is_correct' => $validated['is_correct'] ?? false,
            'graded_at' => now(),
            'graded_by' => auth()->id(),
        ]);

        // Recalculate attempt total score
        $this->gradingService->recalculateAttemptScore($answer->attempt);

        return response()->json([
            'success' => true,
            'message' => 'Answer graded successfully',
            'marks_awarded' => $answer->marks_awarded,
            'grader_comments' => $answer->grader_comments,
            'question_points' => $answer->question->points,
            'current_score' => $answer->attempt->total_score
        ]);
    }

    /**
     * Reset grading for specific answer
     */
    public function resetGrading(ExamAnswer $answer)
    {
        $answer->update([
            'marks_awarded' => null,
            'grader_comments' => null,
            'graded_at' => null,
            'is_correct' => null,
            'is_graded' => false,
            'graded_by' => null,
        ]);

        // Recalculate attempt total score
        $this->gradingService->recalculateAttemptScore($answer->attempt);

        return response()->json([
            'success' => true,
            'message' => 'Grading reset successfully',
            'current_score' => $answer->attempt->total_score
        ]);
    }
}
