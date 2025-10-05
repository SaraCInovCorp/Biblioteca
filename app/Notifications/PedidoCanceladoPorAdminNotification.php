<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Encomenda;

class PedidoCanceladoPorAdminNotification extends Notification
{
    use Queueable;
    protected $pedido;
    /**
     * Create a new notification instance.
     */
    public function __construct(Encomenda $pedido)
    {
        $this->pedido = $pedido;
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
        return (new MailMessage)
            ->subject('Seu pedido foi cancelado')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Seu pedido #' . $this->pedido->id . ' foi cancelado pelo administrador.')
            ->line('Se precisar de mais informações ou suporte, estamos à disposição.');
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
