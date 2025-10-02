<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\BookRequest;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use App\Http\Livewire\Profile\ManageAddresses;
use App\Http\Livewire\Profile\UpdateAddressForm;
use App\Http\Livewire\Profile\UpdateDocumentForm;
use App\Http\Livewire\Profile\ListaEnderecosCheckout;

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

        Livewire::component('profile.update-address-form', UpdateAddressForm::class);
        Livewire::component('profile.update-document-form', UpdateDocumentForm::class);
        Livewire::component('profile.manage-addresses', ManageAddresses::class);
        Livewire::component('profile.lista-enderecos-checkout', ListaEnderecosCheckout::class);
    }
}
