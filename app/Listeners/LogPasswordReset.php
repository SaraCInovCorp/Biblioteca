<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogPasswordReset
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PasswordReset $event): void
    {
        \Log::info('Entrou no listener LogPasswordReset para usuário: '.$event->user->id);

        try {
            activity()
                ->causedBy($event->user)
                ->performedOn($event->user)
                ->event('reset')
                ->useLog('auth')
                ->withProperties([
                    'ip' => request()->ip() ?? 'ip não disponível',
                    'browser' => request()->header('User-Agent') ?? 'browser não disponível',
                ])
                ->log('Password alterado com sucesso');

            \Log::info('Log de atividade gravado com sucesso para usuário: '.$event->user->id);
        } catch (\Throwable $e) {
            \Log::error('Erro ao gravar log de atividade no listener LogPasswordReset: '.$e->getMessage());
        }

    }
}
