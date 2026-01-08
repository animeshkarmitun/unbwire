<?php

namespace App\Mail;

use App\Models\News;
use App\Models\User;
use App\Services\UserNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsPublishedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public News $news;
    public array $contentOptions;
    public array $templateSettings;
    public string $unsubscribeToken;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, News $news)
    {
        $this->user = $user;
        $this->news = $news;
        $this->unsubscribeToken = $user->unsubscribe_token ?? '';
        
        // Get subscription tier and determine content access
        $service = app(UserNotificationService::class);
        $this->contentOptions = $service->getEmailContentForUser($user, $news);
        
        // Get email template settings
        $this->templateSettings = [
            'send_full_content' => getSetting('email_template_send_full_content', '1') === '1',
            'include_title' => getSetting('email_template_include_title', '1') === '1',
            'include_image' => getSetting('email_template_include_image', '1') === '1',
            'include_content' => getSetting('email_template_include_content', '1') === '1',
            'include_author' => getSetting('email_template_include_author', '1') === '1',
            'include_category' => getSetting('email_template_include_category', '1') === '1',
            'include_date' => getSetting('email_template_include_date', '1') === '1',
            'include_video' => getSetting('email_template_include_video', '1') === '1',
            'include_tags' => getSetting('email_template_include_tags', '0') === '1',
            'include_excerpt' => getSetting('email_template_include_excerpt', '1') === '1',
        ];
        
        // If send_full_content is enabled and user can access, ensure full content is included
        if ($this->templateSettings['send_full_content'] && $this->contentOptions['canAccessNews']) {
            $this->contentOptions['includeFullContent'] = true;
            // Also ensure images are included if user has access
            if ($this->contentOptions['subscriberTierLevel'] >= 1) {
                $this->contentOptions['includeImages'] = true;
            }
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $siteName = getSetting('site_name') ?? 'UNB News';
        $fromAddress = config('mail.from.address') ?? 'noreply@unb.com.bd';
        $fromName = config('mail.from.name') ?? $siteName;
        
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($fromAddress, $fromName),
            subject: "{$siteName} - New Article: {$this->news->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.news-published',
            with: [
                'news' => $this->news,
                'user' => $this->user,
                'contentOptions' => $this->contentOptions,
                'templateSettings' => $this->templateSettings,
                'unsubscribeLink' => route('user.unsubscribe', ['token' => $this->unsubscribeToken]),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
