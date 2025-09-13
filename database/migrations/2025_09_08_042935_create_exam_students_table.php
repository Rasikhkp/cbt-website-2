<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->datetime('assigned_at');
            $table->datetime('due_date')->nullable();
            $table->boolean('is_optional')->default(false);
            $table->text('special_instructions')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_students');
    }
};
