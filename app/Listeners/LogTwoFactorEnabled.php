<?php

namespace App\Listeners;

use App\Events\Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogTwoFactorEnabled
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
    public function handle(TwoFactorAuthenticationEnabled $event): void
    {
        \Log::info('Listener LogTwoFactorEnabled executado para usuário ' . $event->user->id);

        activity()
        ->causedBy($event->user)
        ->event('two_factor_enabled')
        ->log('Autenticação de dois fatores habilitada');
    }
}
