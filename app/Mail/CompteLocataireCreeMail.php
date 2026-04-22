<?php

namespace App\Mail;

use App\Models\Locataire;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompteLocataireCreeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Locataire $locataire,
        public readonly string $motDePasseTemporaire,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre compte locataire a été créé — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.compte-locataire-cree',
        );
    }
}
