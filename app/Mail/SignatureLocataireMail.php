<?php

namespace App\Mail;

use App\Models\EtatDesLieux;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SignatureLocataireMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EtatDesLieux $etatDesLieux
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Signature requise - État des lieux ' . $this->etatDesLieux->logement->nom,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.signature-locataire',
        );
    }
}