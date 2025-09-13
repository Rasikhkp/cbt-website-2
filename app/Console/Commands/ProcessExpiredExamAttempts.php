<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessExpiredAttempts;

class ProcessExpiredExamAttempts extends Command
{
    protected $signature = 'exams:process-expired';
    protected $description = 'Process and auto-submit expired exam attempts';

    public function handle()
    {
        $this->info('Processing expired exam attempts...');

        ProcessExpiredAttempts::dispatch();

        $this->info('Expired exam attempts processing job dispatched.');

        return 0;
    }
}
