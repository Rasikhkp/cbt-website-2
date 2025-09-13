<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('answer_text')->nullable(); // For short and long answers
            $table->json('selected_options')->nullable(); // For MCQ answers (array of option IDs)
            $table->decimal('marks_awarded', 5, 2)->nullable(); // Marks given for this answer
            $table->boolean('is_correct')->nullable(); // For auto-gradeable questions
            $table->boolean('is_graded')->default(false);
            $table->text('grader_comments')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('graded_at')->nullable();
            $table->datetime('answered_at')->nullable(); // When student answered
            $table->json('answer_metadata')->nullable(); // Additional data like time spent
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
            $table->index(['is_graded']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_answers');
    }
};
