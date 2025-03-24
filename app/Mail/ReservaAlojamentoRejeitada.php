<?php

namespace App\Mail;

use App\Models\Alojamento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservaAlojamentoRejeitada extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $alojamento;

    /**
     * Create a new message instance.
     */
    public function __construct(Alojamento $alojamento)
    {
        $this->alojamento = $alojamento;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reserva de Alojamento Não Aprovada',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.alojamento.reserva-rejeitada',
            with: [
                'alojamento' => $this->alojamento,
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