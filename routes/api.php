<?php
/**/
/* use Illuminate\Http\Request; */
/* use Illuminate\Support\Facades\Route; */
/* use App\Http\Controllers\Student\ExamController; */
/* use App\Http\Controllers\Student\ExamAttemptController; */
/**/
/* // Student Exam Routes */
/* Route::middleware(['auth:sanctum', 'role:student'])->prefix('student')->group(function () { */
/**/
/*     // Exam listing and details */
/*     Route::get('/exams', [ExamController::class, 'index']); */
/*     Route::get('/exams/{exam}', [ExamController::class, 'show']); */
/**/
/*     // Exam attempts */
/*     Route::post('/exams/{exam}/start', [ExamAttemptController::class, 'start']); */
/*     Route::get('/attempts/{attempt}', [ExamAttemptController::class, 'show']); */
/*     Route::get('/attempts/{attempt}/questions/{question}', [ExamAttemptController::class, 'getQuestion']); */
/*     Route::post('/attempts/{attempt}/questions/{question}/answer', [ExamAttemptController::class, 'saveAnswer']); */
/*     Route::post('/attempts/{attempt}/submit', [ExamAttemptController::class, 'submit']); */
/*     Route::get('/attempts/{attempt}/results', [ExamAttemptController::class, 'getResults']); */
/*     Route::get('/attempts/{attempt}/time', [ExamAttemptController::class, 'getTimeRemaining']); */
/* }); */
/**/
/* // Or for web routes with JSON responses (if using web authentication) */
/* Route::middleware(['auth', 'role:student'])->prefix('api/student')->group(function () { */
/*     // Same routes as above */
/*     Route::get('/exams', [ExamController::class, 'getAvailableExams']); */
/*     Route::get('/exams/{exam}', [ExamController::class, 'getExamDetails']); */
/**/
/*     Route::post('/exams/{exam}/start', [ExamAttemptController::class, 'start']); */
/*     Route::get('/attempts/{attempt}', [ExamAttemptController::class, 'show']); */
/*     Route::get('/attempts/{attempt}/questions/{question}', [ExamAttemptController::class, 'getQuestion']); */
/*     Route::post('/attempts/{attempt}/questions/{question}/answer', [ExamAttemptController::class, 'saveAnswer']); */
/*     Route::post('/attempts/{attempt}/submit', [ExamAttemptController::class, 'submit']); */
/*     Route::get('/attempts/{attempt}/results', [ExamAttemptController::class, 'getResults']); */
/*     Route::get('/attempts/{attempt}/time', [ExamAttemptController::class, 'getTimeRemaining']); */
/* }); */
