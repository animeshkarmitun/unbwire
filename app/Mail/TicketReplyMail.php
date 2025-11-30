<?php

namespace App\Mail;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $reply;
    public $isAdminReply;

    /**
     * Create a new message instance.
     */
    public function __construct(SupportTicket $ticket, SupportTicketReply $reply, bool $isAdminReply = false)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
        $this->isAdminReply = $isAdminReply;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $fromAddress = config('mail.from.address') ?? 'noreply@unb.com.bd';
        $fromName = config('mail.from.name') ?? (getSetting('site_name') ?? 'UNB News');
        
        $subject = $this->isAdminReply
            ? "New Reply on Ticket: {$this->ticket->ticket_number}"
            : "New Reply on Ticket: {$this->ticket->ticket_number}";
        
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($fromAddress, $fromName),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.ticket-reply',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
