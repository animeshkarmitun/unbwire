<?php

namespace App\Services;

use App\Jobs\BulkNotifyUsersJob;
use App\Models\News;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UserNotificationService
{
    /**
     * Notify all eligible users about published news
     */
    public function notifyUsers(News $news, array $options = []): void
    {
        // Check global email notification setting
        $globalEmailEnabled = getSetting('notification_email_enabled', '1') === '1';
        
        // Check if admin wants to send notifications (default: true, or use global setting)
        $sendNotifications = $options['send_notifications'] ?? $globalEmailEnabled;
        
        if (!$sendNotifications) {
            Log::info("Notifications disabled by admin settings for news ID: {$news->id}");
            return;
        }

        // Get eligible users based on subscription access
        $users = $this->getEligibleUsers($news, $options);

        if ($users->isEmpty()) {
            Log::info("No eligible users found for news ID: {$news->id}");
            return;
        }

        // Create notifications for all eligible users
        foreach ($users as $user) {
            $this->createNotification($user, $news);
        }

        // Queue email jobs in batches
        $sendEmails = $options['send_emails'] ?? true;
        if ($sendEmails) {
            $this->queueEmailNotifications($users, $news);
        }
    }

    /**
     * Get eligible users who can access this news
     */
    public function getEligibleUsers(News $news, array $filters = []): Collection
    {
        $query = User::where('email_notifications_enabled', true);

        // Get all and filter by subscription access
        $users = $query->get();

        return $users->filter(function($user) use ($news) {
            return $user->canAccessNews($news);
        });
    }

    /**
     * Create a notification for a user
     */
    public function createNotification(User $user, News $news): UserNotification
    {
        $notification = UserNotification::create([
            'user_id' => $user->id,
            'news_id' => $news->id,
            'title' => $news->title,
            'message' => $this->getNotificationMessage($news),
            'type' => $news->is_breaking_news ? 'breaking_news' : 'news_published',
            'is_read' => false,
        ]);

        // Update user's last notified timestamp
        $user->update(['last_notified_at' => now()]);

        return $notification;
    }

    /**
     * Get notification message
     */
    protected function getNotificationMessage(News $news): string
    {
        $excerpt = strip_tags($news->content);
        return \Str::limit($excerpt, 200);
    }

    /**
     * Queue email notifications in batches
     */
    protected function queueEmailNotifications(Collection $users, News $news): void
    {
        // Process in batches
        $batchSize = (int) (getSetting('notification_batch_size') ?? 100);
        $users->chunk($batchSize)->each(function ($chunk) use ($news) {
            BulkNotifyUsersJob::dispatch($chunk->pluck('id')->toArray(), $news->id);
        });
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(UserNotification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotificationsCount();
    }

    /**
     * Get email content flags for user based on subscription package permissions
     */
    public function getEmailContentForUser(User $user, News $news): array
    {
        $package = $user->currentPackage();
        $tierLevel = $user->getSubscriptionTierLevel();
        $newsRequiredTier = $this->getRequiredTierLevel($news);

        // Initialize all flags to false - only enable based on subscription package permissions
        $includeFullContent = false;
        $includeImages = false;
        $includeVideos = false;
        $includeExclusive = false;

        // First check if user can access this news at all
        if (!$user->canAccessNews($news)) {
            // User cannot access this news, return all false
            return [
                'includeFullContent' => false,
                'includeImages' => false,
                'includeVideos' => false,
                'includeExclusive' => false,
                'subscriberTierLevel' => $tierLevel,
                'newsRequiredTier' => $newsRequiredTier,
                'canAccessNews' => false,
                'templateSettings' => $this->getEmailTemplateSettings(),
            ];
        }

        // User can access the news - now determine what content they can see based on package permissions
        // Check if user wants to receive full content (default: true)
        $sendFullContent = $user->send_full_news_email ?? true;

        // Check package permissions dynamically
        if ($package) {
            // Check if package has access to news (should be true if we got here, but double-check)
            if ($package->hasAccess('news')) {
                $includeFullContent = $sendFullContent;
            }

            // Check if package has access to images
            if ($package->hasAccess('images')) {
                $includeImages = true;
            }

            // Check if package has access to videos
            if ($package->hasAccess('videos')) {
                $includeVideos = true;
            }

            // Check if package has access to exclusive content
            if ($package->hasAccess('exclusive')) {
                $includeExclusive = true;
            }
        } else {
            // Free user (no package) - only basic content
            $includeFullContent = $sendFullContent;
            // Free users don't get images, videos, or exclusive content
        }

        // Special case: Free news (not exclusive) - everyone gets full content and images
        if (strtolower($news->subscription_required ?? 'free') === 'free' && !$news->is_exclusive) {
            $includeFullContent = $sendFullContent;
            $includeImages = true; // Free news images are public
            // Videos only if package allows or if it's free news with public video
            if (!$package || $package->hasAccess('videos')) {
                $includeVideos = !empty($news->video_url);
            }
        }

        // Exclusive content restrictions - must have exclusive permission
        if ($news->is_exclusive) {
            if (!$package || !$package->hasAccess('exclusive')) {
                // No package or no exclusive permission - cannot access exclusive content
                $includeFullContent = false;
                $includeImages = false;
                $includeVideos = false;
                $includeExclusive = false;
            } else {
                // Has exclusive permission - can access everything
                $includeFullContent = $sendFullContent;
                $includeImages = $package->hasAccess('images');
                $includeVideos = $package->hasAccess('videos');
                $includeExclusive = true;
            }
        }

        return [
            'includeFullContent' => $includeFullContent,
            'includeImages' => $includeImages,
            'includeVideos' => $includeVideos,
            'includeExclusive' => $includeExclusive,
            'subscriberTierLevel' => $tierLevel,
            'newsRequiredTier' => $newsRequiredTier,
            'canAccessNews' => true,
            'templateSettings' => $this->getEmailTemplateSettings(),
        ];
    }

    /**
     * Get email template settings
     */
    protected function getEmailTemplateSettings(): array
    {
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        return [
            'send_full_content' => (bool) ($settings['email_template_send_full_content'] ?? true),
            'include_title' => (bool) ($settings['email_template_include_title'] ?? true),
            'include_image' => (bool) ($settings['email_template_include_image'] ?? true),
            'include_content' => (bool) ($settings['email_template_include_content'] ?? true),
            'include_excerpt' => (bool) ($settings['email_template_include_excerpt'] ?? true),
            'include_author' => (bool) ($settings['email_template_include_author'] ?? true),
            'include_category' => (bool) ($settings['email_template_include_category'] ?? true),
            'include_publish_date' => (bool) ($settings['email_template_include_publish_date'] ?? true),
            'include_video_link' => (bool) ($settings['email_template_include_video_link'] ?? true),
            'include_tags' => (bool) ($settings['email_template_include_tags'] ?? true),
        ];
    }

    /**
     * Helper method to get required tier level for news
     */
    protected function getRequiredTierLevel(News $news): int
    {
        if ($news->is_exclusive) {
            return 3; // Ultra tier required for exclusive content
        }

        return match(strtolower($news->subscription_required ?? 'free')) {
            'free' => 0,
            'lite' => 1,
            'pro' => 2,
            'ultra' => 3,
            default => 0,
        };
    }
}
