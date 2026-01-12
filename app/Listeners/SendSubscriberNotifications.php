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
        
        // Merge options from event with defaults
        $options = $event->options ?? [];
        
        // If 'send_emails' is not explicitly set in event options, use global setting
        if (!isset($options['send_emails'])) {
            $options['send_emails'] = $globalEmailEnabled;
        }
        
        $this->notificationService->notifyUsers($event->news, $options);
    }
}
