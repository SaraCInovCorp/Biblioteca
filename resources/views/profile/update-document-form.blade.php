<x-form-section submit="updateDocument">
    <x-slot name="title">
        {{ __('Documentação do Usuário') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Atualize seus dados pessoais e documentos oficiais.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-3">
            <x-label for="data_nascimento" value="{{ __('Data de Nascimento') }}" />
            <x-input id="data_nascimento" type="date" class="mt-1 block w-full" wire:model.defer="state.data_nascimento" />
            <x-input-error for="data_nascimento" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-label for="genero" value="{{ __('Género') }}" />
            <x-select
                label="Género"
                :options="[
                    '' => 'Selecione',
                    'Masculino' => 'Masculino',
                    'Feminino' => 'Feminino',
                    'Outro' => 'Outro'
                ]"
                wire:model.defer="state.genero"
                id="genero"
            />
            <x-input-error for="genero" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-label for="tipo_documento" value="{{ __('Tipo de Documento') }}" />
            <x-select
                label="Tipo de Documento"
                :options="[
                    '' => 'Selecione',
                    'BI' => 'BI',
                    'CC' => 'CC',
                    'Passaporte' => 'Passaporte',
                ]"
                wire:model.defer="state.tipo_documento"
                id="tipo_documento"
            />
            <x-input-error for="tipo_documento" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-label for="numero_documento" value="{{ __('Número do Documento') }}" />
            <x-input id="numero_documento" type="text" class="mt-1 block w-full" wire:model.defer="state.numero_documento" />
            <x-input-error for="numero_documento" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-label for="data_emissao" value="{{ __('Data de Emissão') }}" />
            <x-input id="data_emissao" type="date" class="mt-1 block w-full" wire:model.defer="state.data_emissao" />
            <x-input-error for="data_emissao" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-label for="data_validade" value="{{ __('Data de Validade') }}" />
            <x-input id="data_validade" type="date" class="mt-1 block w-full" wire:model.defer="state.data_validade" />
            <x-input-error for="data_validade" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-label for="entidade_emissora" value="{{ __('Entidade Emissora') }}" />
            <x-input id="entidade_emissora" type="text" class="mt-1 block w-full" wire:model.defer="state.entidade_emissora" />
            <x-input-error for="entidade_emissora" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-label for="nacionalidade" value="{{ __('Nacionalidade') }}" />
            <x-input id="nacionalidade" type="text" class="mt-1 block w-full" wire:model.defer="state.nacionalidade" />
            <x-input-error for="nacionalidade" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-3">
            @if ($saved)
                <div class="alert alert-success">Documento salvo com sucesso!</div>
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved" />
        <x-button>
            {{ __('Salvar Documentação') }}
        </x-button>
    </x-slot>
</x-form-section>
