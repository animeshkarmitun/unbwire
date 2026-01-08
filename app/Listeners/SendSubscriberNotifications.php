<?php

namespace App\Listeners;

use App\Events\NewsPublished;
use App\Services\UserNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSubscriberNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected UserNotificationService $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(UserNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(NewsPublished $event): void
    {
        // Check global email notification setting
        $globalEmailEnabled = getSetting('notification_email_enabled', '1') === '1';
        
        $this->notificationService->notifyUsers($event->news, [
            'send_emails' => $globalEmailEnabled,
        ]);
    }
}
