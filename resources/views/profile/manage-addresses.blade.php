<x-form-section submit="saveAddress">
    <x-slot name="title">
        {{ __('Endereços') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Gerencie seus endereços de entrega e pagamento abaixo. Clique em +Novo Endereço ou Editar para abrir o formulário.') }}
    </x-slot>

    <x-slot name="form">

  <!-- Container principal com 2 linhas: lista e formulário abaixo -->
  <div class="col-span-6 gap-8">

    <!-- Linha 1: Lista de endereços e botão + Novo Endereço -->
    <div class="flex flex-col gap-4">
      <x-button type="button" wire:click="createNewAddress" class="bg-blue-600 text-white">
        + Novo Endereço
      </x-button>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      @foreach ($addresses as $address)
        
        <div class="float-left bg-white p-3 border rounded mb-4 break-words whitespace-normal min-w-48">
          <span class="font-bold block">{{ ucfirst($address['tipo']) }}</span>
          <span class="block">{{ $address['logradouro'] }}, {{ $address['numero'] }}</span>
          <span class="block mb-2">{{ $address['localidade'] }}</span>
          <x-secondary-button type="button"
            wire:click="editAddress({{ $address['id'] }})"
            class="hover:bg-green-600 hover:text-white">
            Editar
          </x-secondary-button>
          <x-secondary-button type="button" 
            wire:click="deleteAddress({{ $address['id'] }})" 
            class="hover:bg-red-600 hover:text-white">
            Excluir
          </x-secondary-button>
        </div>
      @endforeach
      </div>
    </div>

    <!-- Linha 2: Formulário condicional (novo ou edição) -->
    @if ($showForm)
    @if($saved)
    <div class="alert alert-success">Endereço salvo com sucesso!</div>
    @endif
    @if ($successMessage)
      <div class="mb-4 text-green-600 font-semibold">
        {{ $successMessage }}
      </div>
    @endif
      <div class="col-span-6 bg-gray-50 rounded p-6 mx-auto">
                  <div class="col-span-2">
            <h3 class="text-lg font-bold mb-4">
              {{ $editingAddressId ? 'Editar Endereço' : 'Novo Endereço' }}
            </h3>
          </div>
        

          <form wire:submit.prevent="saveAddress" class="">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">    
          <!-- Tipo -->
              <div class="">
                <x-label for="tipo" value="Tipo" />
                <select wire:model.defer="state.tipo" id="tipo" class="mt-1 w-full">
                  @foreach ($optionsTipos as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                  @endforeach
                </select>
                @error('state.tipo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
              </div>

              <!-- Logradouro -->
              <div class="">
                <x-label for="logradouro" value="Logradouro" />
                <x-input id="logradouro" type="text" wire:model.defer="state.logradouro" class="mt-1 w-full" />
                @error('state.logradouro') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
              </div>
            <!-- Número -->
            <div class="">
              <x-label for="numero" value="Número" />
              <x-input id="numero" type="text" wire:model.defer="state.numero" class="mt-1 w-full"  />
              @error('state.numero') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Andar / Bloco -->
            <div class="">
              <x-label for="andereco" value="Andar / Bloco (opcional)" />
              <x-input id="andereco" type="text" wire:model.defer="state.andereco" class="mt-1 w-full"  />
              @error('state.andereco') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Freguesia -->
            <div class="">
              <x-label for="freguesia" value="Freguesia (opcional)" />
              <x-input id="freguesia" type="text" wire:model.defer="state.freguesia" class="mt-1 w-full"  />
              @error('state.freguesia') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Localidade -->
            <div class="">
              <x-label for="localidade" value="Localidade" />
              <x-input id="localidade" type="text" wire:model.defer="state.localidade" class="mt-1 w-full"  />
              @error('state.localidade') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Distrito -->
            <div class="">
              <x-label for="distrito" value="Distrito" />
              <x-input id="distrito" type="text" wire:model.defer="state.distrito" class="mt-1 w-full" />
              @error('state.distrito') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Código Postal -->
            <div class="">
              <x-label for="codigo_postal" value="Código Postal" />
              <x-input id="codigo_postal" type="text" wire:model.defer="state.codigo_postal" class="mt-1 w-full" />
              @error('state.codigo_postal') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- País -->
            <div class="">
              <x-label for="pais" value="País" />
              <x-input id="pais" type="text" wire:model.defer="state.pais" class="mt-1 w-full" />
              @error('state.pais') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Telemóvel -->
            <div class="">
              <x-label for="telemovel" value="Telemóvel (opcional)" />
              <x-input id="telemovel" type="text" wire:model.defer="state.telemovel" class="mt-1 w-full" />
              @error('state.telemovel') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>


            </div>
                        <!-- Botões (em linha, ocupando 2 colunas) -->
            <div class="mt-4 gap-2 col-span-2 flex">
              <x-button>
                {{ $editingAddressId ? 'Atualizar Endereço' : 'Salvar Novo Endereço' }}
              </x-button>
              <x-secondary-button type="button" wire:click="cancelForm" >
                Cancelar
              </x-secondary-button>
            </div>
          </form>
        
      </div>
    @endif

  </div> <!-- Fim do container principal -->
</x-slot>

</x-form-section>