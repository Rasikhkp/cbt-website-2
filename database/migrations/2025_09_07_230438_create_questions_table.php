<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['mcq', 'short', 'long']);
            $table->text('question_text');
            $table->text('explanation')->nullable();
            $table->decimal('points', 5, 2)->default(1.00);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->json('tags')->nullable(); // For categorizing questions
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
