<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nova Editora') }}
        </h2>
    </x-slot>
    <div class="flex-1 ">
        <form method="POST" action="{{ route('editoras.store') }}" enctype="multipart/form-data">
                @csrf
            <div class="p-4 rounded border shadow-sm bg-white">
                
                    <div class="flex flex-col-2 space-x-4">
                        <div class="mb-4">
                            <x-label for="nome" :value="__('Nome')" />
                            <x-input id="nome" type="text" name="nome" :value="old('nome')" />
                            @error('nome')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-4">
                            <x-label for="logo"  :value="__('logo')" />
                            <x-input type="file" name="logo" id="logo" accept="image/*"/>
                            @error('logo')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                
            </div>
            <div class="flex flex-row justify-end mt-4 px-4 space-x-4">
                <x-secondary-button as="a" href="{{ route('editoras.index') }}" class="mt-4  py-2 px-4 hover:bg-red-700 hover:text-white ">Cancelar</x-secondary-button>
                <x-button type="submit" class="mt-4  py-2 px-4 ">Salvar Editora</x-button>
            </div>
        </form>
    </div>
</x-app-layout>