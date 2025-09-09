<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\BookRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        BookRequest::created(function ($bookRequest) {
            Log::info('BookRequest criado:', $bookRequest->toArray());
        });
    }
}
