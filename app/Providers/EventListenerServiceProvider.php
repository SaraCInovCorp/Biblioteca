<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\ConfirmedPassword;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use App\Listeners\LogPasswordConfirmed;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Listeners\LogUserRegistered;
use App\Listeners\LogPasswordReset;
use App\Listeners\LogEmailVerified;
use App\Listeners\LogTwoFactorEnabled;
use App\Listeners\LogTwoFactorDisabled;
use App\Listeners\LogTwoFactorChallenged;

class EventListenerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    protected $listen = [
        
        'Laravel\Fortify\Events\TwoFactorAuthenticationEnabled' => [
            LogTwoFactorEnabled::class,
        ],
        'Laravel\Fortify\Events\TwoFactorAuthenticationDisabled' => [
            LogTwoFactorDisabled::class,
        ],
        'Laravel\Fortify\Events\TwoFactorAuthenticationChallenged' => [
            LogTwoFactorChallenged::class,
        ],
        'Illuminate\Auth\Events\ConfirmedPassword' => [
            LogPasswordConfirmed::class,
        ],
        
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        
    }
}
