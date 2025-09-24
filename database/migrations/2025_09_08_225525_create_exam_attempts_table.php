<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('attempt_number')->default(1);
            $table->datetime('started_at');
            $table->datetime('submitted_at')->nullable();
            $table->datetime('expires_at'); // When the attempt expires based on duration
            $table->decimal('total_score', 8, 2)->nullable();
            $table->decimal('percentage_score', 5, 2)->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'expired', 'graded'])->default('in_progress');
            $table->json('question_order')->nullable(); // Randomized question order for this attempt
            $table->timestamps();
            $table->index(['exam_id', 'student_id']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_attempts');
    }
};
