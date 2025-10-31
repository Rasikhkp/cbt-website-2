<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\ExamAttemptController;
use App\Http\Controllers\Teacher\ExamResultController;
use App\Http\Controllers\Teacher\GradingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('dashboard');
});

Route::get('/download-template', function () {
    $path = public_path('templates/questions-template.xlsx');
    return Response::download($path, 'questions-template.xlsx');
})->name('download.template');

Route::post('/upload-file', [QuestionController::class, 'uploadFile']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    // User management routes
    Route::resource('users', UserController::class);
});

// Teacher routes
Route::middleware(['auth', 'role:teacher,admin'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'teacherDashboard'])->name('dashboard');

    // Question management routes
    Route::get('questions/import', [QuestionController::class, 'import'])->name('questions.import');
    Route::resource('questions', QuestionController::class);

    // Exam management routes
    Route::get('exams/filter-questions', [TeacherExamController::class, 'filterQuestions'])->name('exams.filter-questions');
    Route::resource('exams', TeacherExamController::class);
    Route::patch('exams/{exam}/publish', [TeacherExamController::class, 'publish'])->name('exams.publish');
    Route::patch('exams/{exam}/unpublish', [TeacherExamController::class, 'unpublish'])->name('exams.unpublish');

    // Grading management routes
    Route::post('grading/answers/{answer}/grade', [GradingController::class, 'gradeAnswer'])->name('grade-answer');
    Route::post('grading/answers/{answer}/reset', [GradingController::class, 'resetGrading'])->name('reset-grading');

    // Results management routes
    Route::get('results', [ExamResultController::class, 'index'])->name('results.index');
    Route::get('results/{exam}', [ExamResultController::class, 'show'])->name('results.show');
    Route::get('results/{attempt}/review', [GradingController::class, 'attempt'])->name('results.review');
    Route::post('results/{exam}/hide', [ExamResultController::class, 'hide'])->name('results.hide');
    Route::post('results/{attempt}/submit', [ExamResultController::class, 'submit'])->name('results.submit');
    Route::get('results/{exam}/export', [ExamResultController::class, 'export'])->name('results.export');
    Route::post('results/{exam}/release', [ExamResultController::class, 'release'])->name('results.release');
});

// Student routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'studentDashboard'])->name('dashboard');

    // Student exam routes
    Route::get('/exams', [StudentExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/{exam}', [StudentExamController::class, 'show'])->name('exams.show');

    // Student exam attempt routes
    Route::post('/exams/{exam}/start', [ExamAttemptController::class, 'start'])->name('exams.start');
    Route::get('/attempts/{attempt}', [ExamAttemptController::class, 'show'])->name('attempts.show');
    Route::get('/attempts/{attempt}/take', [ExamAttemptController::class, 'take'])->name('attempts.take');
    Route::get('/attempts/{attempt}/questions/{questionNumber}', [ExamAttemptController::class, 'question'])->name('attempts.question');
    Route::post('/attempts/{attempt}/save-answer', [ExamAttemptController::class, 'saveAnswer'])->name('attempts.save-answer');
    Route::post('/attempts/{attempt}/submit', [ExamAttemptController::class, 'submit'])->name('attempts.submit');
    Route::get('/attempts/{attempt}/results', [ExamAttemptController::class, 'results'])->name('attempts.results');

    // AJAX routes for real-time functionality
    Route::get('/attempts/{attempt}/time-remaining', [ExamAttemptController::class, 'getTimeRemaining'])->name('attempts.time-remaining');
    Route::post('/attempts/{attempt}/auto-save', [ExamAttemptController::class, 'autoSaveAnswer'])->name('attempts.auto-save');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
