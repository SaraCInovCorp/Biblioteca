<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Alterar o Password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Certifique-se de usar uma senha longa e forte para manter sua conta segura. Sua senha deve ter pelo menos 8 caracteres, incluir letras maiúsculas e minúsculas, números e símbolos..') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="{{ __('Atual Password') }}" />
            <x-password-input id="current_password" class="mt-1" required wire:model="state.current_password" autocomplete="current-password" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="{{ __('Novo Password') }}" />
            <x-password-input id="password" class="mt-1" name="password" required wire:model="state.password" autocomplete="new-password" />
            <x-input-error for="password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="{{ __('Confirma Password') }}" />
            <x-password-input id="password_confirmation" class="mt-1" required wire:model="state.password_confirmation" autocomplete="new-password" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Salvo.') }}
        </x-action-message>

        <x-button>
            {{ __('Salvar') }}
        </x-button>
    </x-slot>
</x-form-section>
