<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PedidoCancelamentoStatusNotification extends Notification
{
    use Queueable;
    protected $pedido;
    protected $aprovado;

    /**
     * Create a new notification instance.
     */
    public function __construct($pedido, bool $aprovado)
    {
        $this->pedido = $pedido;
        $this->aprovado = $aprovado;
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
        if ($this->aprovado) {
            return (new MailMessage)
                ->subject('Seu cancelamento foi aprovado')
                ->greeting('Olá ' . $notifiable->name . ',')
                ->line('Seu pedido #' . $this->pedido->id . ' teve o cancelamento aprovado.')
                ->line('Você receberá o reembolso em breve na forma de pagamento utilizada.')
                ->line('Obrigado por usar nossa plataforma!');
        } else {
            return (new MailMessage)
                ->subject('Seu cancelamento foi rejeitado')
                ->greeting('Olá ' . $notifiable->name . ',')
                ->line('Sua solicitação de cancelamento para o pedido #' . $this->pedido->id . ' foi rejeitada.')
                ->line('Caso tenha alguma dúvida, entre em contato conosco.')
                ->line('Obrigado por usar nossa plataforma!');
        }
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
