<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->integer('order')->default(0);
            $table->text('alt_text')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_images');
    }
};
