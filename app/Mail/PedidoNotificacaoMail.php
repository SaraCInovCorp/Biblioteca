<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\BookRequest;

class PedidoNotificacaoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bookRequest;

    public function __construct(BookRequest $bookRequest)
    {
        $this->bookRequest = $bookRequest;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova Requisição de Livro Realizada',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.pedido_notificacao',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
