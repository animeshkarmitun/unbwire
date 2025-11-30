<?php

namespace App\Mail;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $isAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct(SupportTicket $ticket, bool $isAdmin = false)
    {
        $this->ticket = $ticket;
        $this->isAdmin = $isAdmin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $fromAddress = config('mail.from.address') ?? 'noreply@unb.com.bd';
        $fromName = config('mail.from.name') ?? (getSetting('site_name') ?? 'UNB News');
        
        $subject = $this->isAdmin 
            ? "New Support Ticket Created: {$this->ticket->ticket_number}"
            : "Support Ticket Created: {$this->ticket->ticket_number}";
        
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
            view: 'mail.ticket-created',
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
