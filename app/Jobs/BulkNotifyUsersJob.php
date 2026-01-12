<?php

namespace App\Jobs;

use App\Models\News;
use App\Models\User;
use App\Jobs\SendUserNotificationJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BulkNotifyUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $userIds;
    public int $newsId;
    public int $delayBetweenBatches;

    /**
     * Create a new job instance.
     */
    public function __construct(array $userIds, int $newsId, int $delayBetweenBatches = 5)
    {
        $this->userIds = $userIds;
        $this->newsId = $newsId;
        $this->delayBetweenBatches = $delayBetweenBatches;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $news = News::find($this->newsId);
        if (!$news) {
            Log::warning("BulkNotifyUsersJob: News with ID {$this->newsId} not found.");
            return;
        }

        $users = User::whereIn('id', $this->userIds)->get();
        $chunkSize = (int) (getSetting('notification_batch_size') ?? 500);

        foreach ($users->chunk($chunkSize) as $chunk) {
            foreach ($chunk as $user) {
                // Ensure user is still eligible and wants emails
                if ($user->shouldReceiveEmail() && $user->canAccessNews($news)) {
                    SendUserNotificationJob::dispatch($user, $news)
                        ->onQueue('emails');
                }
            }
            // Apply delay between chunks to respect rate limits
            if ($this->delayBetweenBatches > 0) {
                sleep($this->delayBetweenBatches);
            }
        }
        Log::info("Bulk email notification job completed for news ID: {$this->newsId}");
    }
}
