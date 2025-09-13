<?php

namespace App\Events;

use App\Models\ExamAttempt;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamAttemptStarted
{
    use Dispatchable, SerializesModels;

    public function __construct(public ExamAttempt $attempt) {}
}
