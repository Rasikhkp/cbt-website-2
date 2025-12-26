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
        $exams = Exam::with(['attempts' => function ($query) {
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
    public function show(Request $request, Exam $exam)
    {
        $filters = $request->only(['search', 'status', 'grade', 'suspicious']);

        $attemptsQuery = ExamAttempt::where('exam_id', $exam->id)
            ->with([
                'student',
                'answers' => function ($query) {
                    $query->with('question');
                }
            ]);

        // Search by name or email
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $attemptsQuery->whereHas('student', function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $attemptsQuery->where('status', $filters['status']);
        }

        // Filter by grade
        if (!empty($filters['grade'])) {
            $grade = $filters['grade'];
            $maxScore = $exam->total_marks ?: 100;

            if ($maxScore > 0) {
                $gradeBoundaries = [
                    'A' => [90, 100],
                    'B' => [80, 89.99],
                    'C' => [70, 79.99],
                    'D' => [60, 69.99],
                    'F' => [0, 59.99],
                ];

                if (array_key_exists($grade, $gradeBoundaries)) {
                    $lowerBound = ($gradeBoundaries[$grade][0] / 100) * $maxScore;
                    $upperBound = ($gradeBoundaries[$grade][1] / 100) * $maxScore;
                    $attemptsQuery->whereBetween('total_score', [$lowerBound, $upperBound]);
                }
            }
        }

        // Filter by suspicious behaviours count
        if (!empty($filters['suspicious'])) {
            $suspicious = $filters['suspicious'];
            if ($suspicious === 'none') {
                $attemptsQuery->where(function ($query) {
                    $query->whereNull('suspicious_behaviours')
                        ->orWhere(DB::raw('JSON_LENGTH(suspicious_behaviours)'), '=', 0);
                });
            } elseif ($suspicious === '1-10') {
                $attemptsQuery->where(DB::raw('JSON_LENGTH(suspicious_behaviours)'), '>=', 1)
                    ->where(DB::raw('JSON_LENGTH(suspicious_behaviours)'), '<=', 10);
            } elseif ($suspicious === '10+') {
                $attemptsQuery->where(DB::raw('JSON_LENGTH(suspicious_behaviours)'), '>=', 10);
            }
        }

        $attempts = $attemptsQuery->orderBy('total_score', 'desc')->get();

        $statistics = $this->resultService->getExamStatistics($exam);

        return view('teacher.results.show', compact('exam', 'attempts', 'statistics', 'filters'));
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

    /**
     * Add extra time to all active exam attempts
     */
    public function addTimeToAll(Request $request, Exam $exam)
    {
        $request->validate([
            'minutes' => 'required|integer|min:1',
        ]);

        // Update exam duration for future attempts
        $exam->increment('duration_minutes', $request->minutes);

        // Update active attempts
        $count = $exam->attempts()
            ->where('status', 'in_progress')
            ->get()
            ->each(function ($attempt) use ($request) {
                $attempt->expires_at = $attempt->expires_at->addMinutes((int) $request->minutes);
                $attempt->save();
            })
            ->count();

        return redirect()->back()->with('success', "Successfully added {$request->minutes} minutes to {$count} active attempts.");
    }
}
