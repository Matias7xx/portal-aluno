<?php

namespace App\Mail;

use App\Models\Matricula;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MatriculaAprovada extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $matricula;

    /**
     * Create a new message instance.
     */
    public function __construct(Matricula $matricula)
    {
        $this->matricula = $matricula;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Matrícula Aprovada - ACADEPOL',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.matricula.aprovada',
            with: [
                'matricula' => $this->matricula,
                'curso' => $this->matricula->curso,
                'aluno' => $this->matricula->aluno,
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