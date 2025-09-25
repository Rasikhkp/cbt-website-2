<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Services\ResultService;
use App\Http\Requests\ReleaseResultsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamResultController extends Controller
{
    protected $resultService;

    public function __construct(ResultService $resultService)
    {
        $this->resultService = $resultService;
    }

    /**
     * Show results management page
     */
    public function index(Request $request)
    {
        $exams = Exam::where('created_by', auth()->id())
            ->with(['attempts' => function ($query) {
                $query->where('status', 'ccwo')
                    ->with('student');
            }])
            ->withCount([
                'attempts as total_attempts',
                'attempts as graded_attempts' => function ($query) {
                    $query->where('status', 'graded');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.results.index', compact('exams'));
    }

    /**
     * Show detailed results for specific exam
     */
    public function show(Exam $exam)
    {
        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->with([
                'student',
                'answers' => function ($query) {
                    $query->with('question');
                }
            ])
            ->orderBy('total_score', 'desc')
            ->get();

        $statistics = $this->resultService->getExamStatistics($exam);

        return view('teacher.results.show', compact('exam', 'attempts', 'statistics'));
    }

    /**
     * Release results for an exam
     */
    public function release(Request $request, Exam $exam)
    {
        $exam->update([
            'results_released' => true,
            'results_released_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Results have been released successfully',
        ]);
    }

    /**
     * Hide results for an exam
     */
    public function hide(Exam $exam)
    {
        $exam->update([
            'results_released' => false,
            'results_released_at' => null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Results have been hidden from students');
    }

    /**
     * Export results to CSV
     */
    public function export(Exam $exam, Request $request)
    {
        $format = $request->get('format', 'csv');

        return $this->resultService->exportResults($exam, $format);
    }

    public function submit(Request $request, ExamAttempt $attempt)
    {
        try {
            DB::beginTransaction();

            if ($attempt->isSubmitted()) {
                DB::rollback();
                return redirect()->back()->with('info', 'This exam has already been submitted.');
            }

            // Mark attempt as submitted
            $attempt->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Auto-grade the attempt
            $attempt->autoGrade();

            DB::commit();

            return redirect()->back()->with('success', 'Exam submitted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to submit exam: ' . $e->getMessage());
        }
    }
}
