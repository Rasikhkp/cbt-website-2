<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->integer('question_order');
            $table->decimal('marks', 5, 2); // Marks for this question in this exam
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->unique(['exam_id', 'question_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_questions');
    }
};
