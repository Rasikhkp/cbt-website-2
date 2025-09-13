<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ExamAttempt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessExpiredAttempts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Find all attempts that should be expired but are still in progress
        $expiredAttempts = ExamAttempt::where('status', 'in_progress')
            ->where('expires_at', '<=', Carbon::now())
            ->get();

        $processedCount = 0;

        foreach ($expiredAttempts as $attempt) {
            try {
                $attempt->markAsExpired();
                $processedCount++;

                Log::info("Marked attempt {$attempt->id} as expired for student {$attempt->student_id}");
            } catch (\Exception $e) {
                Log::error("Failed to process expired attempt {$attempt->id}: " . $e->getMessage());
            }
        }

        if ($processedCount > 0) {
            Log::info("Processed {$processedCount} expired exam attempts");
        }
    }
}
