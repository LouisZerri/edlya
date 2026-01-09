<?php

namespace App\Mail;

use App\Models\EtatDesLieux;
use App\Models\Partage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartageEtatDesLieux extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EtatDesLieux $etatDesLieux,
        public Partage $partage
    ) {}

    public function envelope(): Envelope
    {
        $type = $this->etatDesLieux->type === 'entree' ? 'entrée' : 'sortie';
        
        return new Envelope(
            subject: "État des lieux d'{$type} - {$this->etatDesLieux->logement->nom}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.partage-etat-des-lieux',
        );
    }
}