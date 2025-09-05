<x-layout>
    <main>
        <div class="py-4 w-full">
            <p class="mr-auto font-bold text-lg">Editora: {{ $editora->nome }}</p>
        </div>
        <div class="p-4 rounded border shadow-sm bg-white">
            <div class="flex items-center">
                <img src="{{ Str::startsWith($editora->logo_url, ['http://','https://']) ? $editora->logo_url : asset('storage/'.$editora->logo_url) }}" alt="{{ $editora->nome }}" class="w-16 h-16 rounded object-cover" />
                <div class="ml-4">
                    <p class="font-semibold">Nome: {{ $editora->nome }}</p>
                </div>
            </div>
        <div class="flex flex-row justify-end mt-4 px-4 space-x-4">
        <x-secondary-button as="a" href="{{ route('editoras.index') }}" class="mt-4  py-2 px-4 ">Voltar</x-secondary-button>    
        <form action="{{ route('editoras.destroy', $editora->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar esta editora?');">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" class=" hover:bg-red-700 text-white font-semibold mt-4 py-2 px-4 ">
                        Deletar
                    </x-button>
                </form>
            
            <x-button type="button" onclick="window.location='{{ route('editoras.edit', $editora) }}'" class="mt-4  py-2 px-4 ">Editar</x-button>
        </div>
    </main>
</x-layout>