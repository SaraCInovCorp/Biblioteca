<x-guest-layout>
    <div class="flex-1 ">
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo size="16"/>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Esta é uma área segura do aplicativo. Por favor, confirme sua senha antes de continuar.') }}
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-password-input id="password" class="mt-1" name="password" required autocomplete="current-password" autofocus/>
            </div>

            <div class="flex justify-end mt-4">
                <x-button class="ms-4">
                    {{ __('Confirma') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
