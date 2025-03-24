<?php

namespace App\Mail;

use App\Models\Contato;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RespostaContato extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $contato;

    /**
     * Create a new message instance.
     */
    public function __construct(Contato $contato)
    {
        $this->contato = $contato;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Resposta ao seu contato - ACADEPOL',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contato.resposta',
            with: [
                'contato' => $this->contato,
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