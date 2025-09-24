<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Livro;

class LivroDisponivelNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(Livro $livro)
    {
        $this->livro = $livro;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $livro = $this->livro;

        return (new MailMessage)
            ->subject("O livro '{$livro->titulo}' está disponível!")
            ->line("O livro '{$livro->titulo}' que você está esperando agora está disponível para requisição.")
            ->action('Veja o livro', url(route('livros.show', $livro->id)))
            ->line('Obrigado por usar nossa biblioteca!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
