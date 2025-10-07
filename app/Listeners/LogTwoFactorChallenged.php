<?php

namespace App\Listeners;

use App\Events\Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogTwoFactorChallenged
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
    public function handle(TwoFactorAuthenticationChallenged $event): void
    {
        $user = $event->user;

        Log::info("Usuário {$user->id} passou pelo desafio de 2FA.");

        activity()
            ->causedBy($user)
            ->event('two_factor_challenged')
            ->log('Desafio da autenticação de dois fatores');
    }
}
