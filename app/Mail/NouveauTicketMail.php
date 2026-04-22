<?php

namespace App\Mail;

use App\Models\TicketMaintenance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NouveauTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly TicketMaintenance $ticket,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Ticket #'.$this->ticket->id.'] '.$this->ticket->titre.' — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nouveau-ticket',
        );
    }
}
