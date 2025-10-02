<x-form-section submit="updateAddress">
    <x-slot name="title">
        {{ __('Endereço') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Atualize os seus endereços de entrega e faturação.') }}
    </x-slot>
    <x-slot name="form">
        <div>
            <x-label for="logradouro" value="Logradouro" />
            <x-input id="logradouro" type="text" 
                     wire:model.defer="state.logradouro" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-button>Salvar Endereço</x-button>
    </x-slot>
</x-form-section>
