<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->integer('duration_minutes'); // Total time allowed
            $table->integer('total_marks')->default(0);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_options')->default(false);
            $table->boolean('show_results_immediately')->default(false);
            $table->boolean('allow_review')->default(true);
            $table->integer('max_attempts')->default(1);
            $table->enum('status', ['draft', 'published', 'completed', 'archived'])->default('draft');
            $table->json('settings')->nullable(); // Additional settings
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exams');
    }
};
