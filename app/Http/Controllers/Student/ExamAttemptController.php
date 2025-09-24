<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StartExamRequest;
use App\Http\Requests\SubmitAnswerRequest;
use App\Http\Requests\SubmitExamRequest;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamAttemptController extends Controller
{
    public function show(ExamAttempt $attempt)
    {
        $user = auth()->user();

        if ($attempt->student_id !== $user->id) {
            abort(403, 'Unauthorized access to exam attempt.');
        }

        // Check if attempt has expired
        if ($attempt->isInProgress() && $attempt->isExpired()) {
            $attempt->markAsExpired();
            $attempt->refresh();
        }

        $questions = $attempt->getQuestionsInOrder();
        $answers = $attempt->answers()->with('question')->get()->keyBy('question_id');

        return view('student.attempts.show', compact('attempt', 'questions', 'answers'));
    }

    public function start(StartExamRequest $request, Exam $exam)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();

            // Check for existing active attempt
            $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
                ->where('student_id', $user->id)
                ->where('status', 'in_progress')
                ->first();

            if ($existingAttempt) {
                DB::rollback();
                return redirect()->route('student.attempts.take', $existingAttempt)
                    ->with('info', 'You already have an active attempt for this exam.');
            }

            // Get next attempt number
            $attemptNumber = ExamAttempt::where('exam_id', $exam->id)
                ->where('student_id', $user->id)
                ->count() + 1;

            // Prepare question order
            $questionOrder = null;
            if ($exam->randomize_questions) {
                $questionIds = $exam->examQuestions()->pluck('question_id')->toArray();
                shuffle($questionIds);
                $questionOrder = $questionIds;
            }

            // Create attempt
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'student_id' => $user->id,
                'attempt_number' => $attemptNumber,
                'started_at' => now(),
                'expires_at' => now()->addMinutes($exam->duration_minutes),
                'status' => 'in_progress',
                'question_order' => $questionOrder,
            ]);

            // Create empty answers for all questions
            $examQuestions = $exam->examQuestions;
            foreach ($examQuestions as $examQuestion) {
                ExamAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $examQuestion->question_id,
                ]);
            }

            DB::commit();

            return redirect()->route('student.attempts.take', $attempt)
                ->with('success', 'Exam started successfully. Good luck!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to start exam: ' . $e->getMessage());
        }
    }

    public function take(ExamAttempt $attempt)
    {
        $user = auth()->user();

        if ($attempt->student_id !== $user->id) {
            abort(403, 'Unauthorized access to exam attempt.');
        }

        // Check if attempt has expired
        if ($attempt->isInProgress() && $attempt->isExpired()) {
            $attempt->markAsExpired();
            return redirect()->route('student.attempts.results', $attempt)
                ->with('warning', 'Your exam time has expired and has been automatically submitted.');
        }

        if (!$attempt->isInProgress()) {
            return redirect()->route('student.attempts.results', $attempt)
                ->with('info', 'This exam attempt has already been submitted.');
        }

        $questions = $attempt->getQuestionsInOrder();
        $answers = $attempt->answers()->get()->keyBy('question_id');

        // Get current question (first unanswered or first question)
        $currentQuestionIndex = 0;
        foreach ($questions as $index => $examQuestion) {
            $answer = $answers->get($examQuestion->question_id);
            if (!$answer || !$answer->isAnswered()) {
                $currentQuestionIndex = $index;
                break;
            }
        }

        return view('student.attempts.take', compact('attempt', 'questions', 'answers', 'currentQuestionIndex'));
    }

    public function question(ExamAttempt $attempt, $questionNumber)
    {
        $user = auth()->user();

        if ($attempt->student_id !== $user->id) {
            abort(403, 'Unauthorized access to exam attempt.');
        }

        // Check if attempt has expired
        if ($attempt->isInProgress() && $attempt->isExpired()) {
            $attempt->markAsExpired();
            return redirect()->route('student.attempts.results', $attempt)
                ->with('warning', 'Your exam time has expired and has been automatically submitted.');
        }

        if (!$attempt->isInProgress()) {
            return redirect()->route('student.attempts.results', $attempt)
                ->with('info', 'This exam attempt has already been submitted.');
        }

        $questions = $attempt->getQuestionsInOrder();
        $questionIndex = $questionNumber - 1; // Convert to 0-based index

        if ($questionIndex < 0 || $questionIndex >= $questions->count()) {
            abort(404, 'Question not found.');
        }

        $currentQuestion = $questions->get($questionIndex);
        $question = $currentQuestion->question;

        // Load question with options and images
        $question->load(['options', 'images']);

        // Randomize options if needed
        if ($attempt->exam->randomize_options && $question->isMCQ()) {
            $question->setRelation('options', $question->options->shuffle());
        }

        $answer = $attempt->answers()->where('question_id', $question->id)->first();
        $answers = $attempt->answers()->get()->keyBy('question_id');

        return view('student.attempts.question', compact(
            'attempt',
            'questions',
            'currentQuestion',
            'question',
            'answer',
            'answers',
            'questionIndex',
            'questionNumber'
        ));
    }

    public function saveAnswer(SubmitAnswerRequest $request, ExamAttempt $attempt)
    {
        try {
            DB::beginTransaction();

            $questionId = $request->question_id;
            $question = Question::findOrFail($questionId);

            // Check if attempt has expired
            if ($attempt->isExpired()) {
                $attempt->markAsExpired();
                DB::rollback();
                return redirect()->route('student.attempts.results', $attempt)
                    ->with('warning', 'Your exam time has expired and has been automatically submitted.');
            }

            $answer = ExamAnswer::where('attempt_id', $attempt->id)
                ->where('question_id', $questionId)
                ->first();

            if (!$answer) {
                DB::rollback();
                return redirect()->back()->with('error', 'Answer record not found.');
            }

            // Prepare answer data
            $answerData = [];

            if ($request->selected_options || $request->answer_text) {
                $answerData['answered_at'] = now();
            }

            if ($question->isMCQ()) {
                $answerData['selected_options'] = $request->selected_options;
                $answerData['answer_text'] = null;
            } else {
                $answerData['answer_text'] = $request->answer_text;
                $answerData['selected_options'] = null;
            }

            $answer->update($answerData);

            DB::commit();

            // Handle navigation
            if ($request->has('action')) {
                $questions = $attempt->getQuestionsInOrder();
                $currentIndex = $questions->search(function ($examQuestion) use ($questionId) {
                    return $examQuestion->question_id == $questionId;
                });

                if ($request->action === 'next' && $currentIndex !== false && $currentIndex < $questions->count() - 1) {
                    $nextQuestionNumber = $currentIndex + 2; // Convert to 1-based
                    return redirect()->route('student.attempts.question', [$attempt, $nextQuestionNumber]);
                } elseif ($request->action === 'previous' && $currentIndex !== false && $currentIndex > 0) {
                    $prevQuestionNumber = $currentIndex; // Convert to 1-based
                    return redirect()->route('student.attempts.question', [$attempt, $prevQuestionNumber]);
                }
            }

            return redirect()->back()->with('success', 'Answer saved successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to save answer: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function submit(SubmitExamRequest $request, ExamAttempt $attempt)
    {
        try {
            DB::beginTransaction();

            if ($attempt->isSubmitted()) {
                DB::rollback();
                return redirect()->route('student.exams.show', $attempt->exam->id)
                    ->with('info', 'This exam has already been submitted.');
            }

            // Mark attempt as submitted
            $attempt->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Auto-grade the attempt
            $attempt->autoGrade();

            DB::commit();

            return redirect()->route('student.exams.show', $attempt->exam->id)
                ->with('success', 'Exam submitted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to submit exam: ' . $e->getMessage());
        }
    }

    public function results(ExamAttempt $attempt)
    {
        $user = auth()->user();

        if ($attempt->student_id !== $user->id) {
            abort(403, 'Unauthorized access to exam results.');
        }

        if (!$attempt->isSubmitted()) {
            return redirect()->route('student.exams.show', $attempt->exam)
                ->with('error', 'This exam has not been submitted yet.');
        }

        $questions = $attempt->getQuestionsInOrder();
        $answers = $attempt->answers()->with('question.options')->get()->keyBy('question_id');

        // Calculate summary statistics
        $summary = [
            'totalQuestions' => $questions->count(),
            'answeredQuestions' => $answers->filter(function ($answer) {
                return $answer->isAnswered();
            })->count(),
            'gradedQuestions' => $answers->where('is_graded', true)->count(),
            'totalMarks' => $attempt->exam->total_marks,
            'scoredMarks' => $attempt->total_score,
            'percentage' => $attempt->percentage_score,
        ];

        return view('student.attempts.results', compact('attempt', 'questions', 'answers', 'summary'));
    }

    // AJAX endpoint for getting remaining time
    public function getTimeRemaining(ExamAttempt $attempt)
    {
        $user = auth()->user();

        if ($attempt->student_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$attempt->isInProgress()) {
            return response()->json([
                'expired' => true,
                'message' => 'Exam is not in progress.'
            ]);
        }

        $remainingSeconds = $attempt->getRemainingTimeSeconds();

        if ($remainingSeconds <= 0 && $attempt->isInProgress()) {
            $attempt->markAsExpired();
            return response()->json([
                'expired' => true,
                'message' => 'Exam has expired.',
                'redirect' => route('student.attempts.results', $attempt)
            ]);
        }

        return response()->json([
            'remainingSeconds' => $remainingSeconds,
            'remainingFormatted' => $attempt->getRemainingTimeFormatted(),
            'expired' => false,
        ]);
    }

    // AJAX endpoint for auto-saving answers
    public function autoSaveAnswer(Request $request, ExamAttempt $attempt)
    {
        $user = auth()->user();

        if ($attempt->student_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$attempt->isInProgress() || $attempt->isExpired()) {
            return response()->json(['error' => 'Exam is not active'], 422);
        }

        try {
            $questionId = $request->question_id;
            $question = Question::findOrFail($questionId);

            $answer = ExamAnswer::where('attempt_id', $attempt->id)
                ->where('question_id', $questionId)
                ->first();

            if (!$answer) {
                return response()->json(['error' => 'Answer record not found'], 404);
            }

            // Prepare answer data
            $answerData = [
                'answered_at' => now()
            ];

            if ($question->isMCQ()) {
                $answerData['selected_options'] = $request->selected_options ?? [];
            } else {
                $answerData['answer_text'] = $request->answer_text;
            }

            $answer->update($answerData);

            $answeredCount = $answer->attempt->answers->whereNotNull('selected_options')->count();
            $totalQuestions = $answer->attempt->answers->count();

            return response()->json([
                'success' => true,
                'message' => 'Answer auto-saved',
                'progress' => $attempt->getProgressPercentage(),
                'answered_at' => "Last saved: " . $answer->answered_at->format('M j, g:i A'),
                'answered_count' => $answeredCount,
                'total_questions' => $totalQuestions
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to auto-save: ' . $e->getMessage()], 500);
        }
    }

    // Helper method to check if student can access attempt
    private function authorizeAttempt(ExamAttempt $attempt)
    {
        $user = auth()->user();

        if ($attempt->student_id !== $user->id) {
            abort(403, 'Unauthorized access to exam attempt.');
        }
    }

    // Helper method to handle expired attempts
    private function handleExpiredAttempt(ExamAttempt $attempt)
    {
        if ($attempt->isInProgress() && $attempt->isExpired()) {
            $attempt->markAsExpired();
            return redirect()->route('student.attempts.results', $attempt)
                ->with('warning', 'Your exam time has expired and has been automatically submitted.');
        }

        return null;
    }
}
