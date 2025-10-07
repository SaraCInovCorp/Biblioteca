<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
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
    public function handle(Login $event): void
    {

        activity()
            ->causedBy($event->user)
            ->performedOn($event->user)
            ->event('login')
            ->useLog('auth')
            ->withProperties([
                'ip' => request()->ip(),
                'browser' => request()->header('User-Agent'),
            ])
            ->log('Usuário logado com sucesso');
    }
}
