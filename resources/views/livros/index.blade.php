<x-layout>
    <main class="flex-grow">
        <div class="py-4 w-full flex justify-between items-center">
            <div>
                <p class="mr-auto font-bold text-lg">Livros</p>
                <p>Total de livros: {{ $livros->total() }}</p>
            </div>
            <div>
                @if(auth()->check() && auth()->user()->isAdmin())
                <x-secondary-button as="a" href="{{ route('livros.create') }}">Novo Livro</x-secondary-button>
                @endif
            </div>
        </div>
        <form method="GET" action="{{ route('livros.index') }}" class="mb-6 flex gap-4 flex-wrap items-end">
    <div>
        <x-label for="query">Buscar título</x-label>
        <x-input type="text" name="query" id="query" value="{{ request('query') }}" />
    </div>

    <div>
        <x-label for="editora">Editora</x-label>
        @php
            $editorasOptions = $editoras->pluck('nome', 'id')->toArray();
        @endphp

        <x-select 
            
            name="editora" 
            id="editora" 
            :options="$editorasOptions" 
            :selected="request('editora')" 
            label="editora" 
        />
        
    </div>

    <div>
        <x-label for="autor">Autor</x-label>

        @php
            $autoresOptions = $autores->pluck('nome', 'id')->toArray();
        @endphp

        <x-select 
            name="autor" 
            id="autor" 
            :options="$autoresOptions" 
            :selected="request('autor')" 
            label="autor" 
        />

    </div>

    <x-button type="submit">Filtrar</x-button>
    <x-secondary-button as="a" href="{{ route('livros.index') }}" class="ml-2 px-4 py-2 border rounded text-gray-700 hover:bg-gray-200">
        Limpar filtros
    </x-secondary-button>
</form>
        
        @if ($livros->isEmpty())
            <p class="text-gray-500">Nenhum livro cadastrado.</p>
        @else
            <div class="grid lg:grid-cols-3 gap-8 mt-6">
                @foreach ($livros as $livro)
                    <x-livro-card :livro="$livro" />
                @endforeach
            </div>
            <div class="mt-6">
                <div class="mb-4 flex justify-end gap-2">
                    <x-secondary-button as="a" href="{{ route('livros.export.excel', request()->query()) }}" class="btn btn-success">
                        Exportar Excel
                    </x-secondary-button>
                    <x-secondary-button as="a" href="{{ route('livros.export.pdf', request()->query()) }}" class="btn btn-danger">
                        Exportar PDF
                    </x-secondary-button>
                </div>

                {{ $livros->links() }}
            </div>
        @endif
    </main>
</x-layout>