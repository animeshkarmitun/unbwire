<?php

namespace App\Jobs;

use App\Mail\NewsPublishedMail;
use App\Models\News;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendUserNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public News $news;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, News $news)
    {
        $this->user = $user;
        $this->news = $news;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Double-check user can access this news and wants emails
            if (!$this->user->shouldReceiveEmail() || !$this->user->canAccessNews($this->news)) {
                Log::info("Skipping email for user {$this->user->email} - either opted out or cannot access news {$this->news->id}");
                return;
            }
            
            // Load relationships needed for email
            $this->news->load(['category', 'auther', 'author', 'tags']);
            
            Mail::to($this->user->email)->send(new NewsPublishedMail($this->user, $this->news));

                // Update email_sent status in user_notifications
                $notification = $this->user->notifications()
                    ->where('news_id', $this->news->id)
                    ->first();

                if ($notification) {
                    $notification->email_sent = true;
                    $notification->email_sent_at = now();
                    $notification->save();
                }
                Log::info("Email sent to user: {$this->user->email} for news: {$this->news->title}");
            } else {
                Log::info("User {$this->user->email} opted out of email notifications.");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send email to user {$this->user->email} for news {$this->news->title}: " . $e->getMessage());
            // Optionally re-throw or handle retry logic
        }
    }
}
