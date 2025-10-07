<?php

namespace App\Listeners;

use App\Events\Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogTwoFactorDisabled
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
    public function handle(TwoFactorAuthenticationDisabled $event): void
    {
        $user = $event->user;

        Log::info("Usuário {$user->id} desabilitou autenticação de dois fatores.");

        activity()
            ->causedBy($user)
            ->event('two_factor_disabled')
            ->log('Autenticação de dois fatores desabilitada');
    }
}
