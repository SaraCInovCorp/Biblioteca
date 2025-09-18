<x-app-layout>
    <div class="flex-grow">
        <form method="GET" action="{{ route('welcome') }}" class="mb-6">
            <x-label for="search">Pesquise seu livro, autor ou editora:</x-label>
            <x-input type="text" name="query" id="search" value="{{ $query }}" class="w-full" placeholder="Digite para pesquisar..."/>
            <x-button type="submit" class="mt-5">Buscar</x-button>
            <x-secondary-button as="a" href="/" class="mt-5 mx-4 px-4 py-2 border rounded text-gray-700 hover:bg-gray-200">
        Limpar filtros
    </x-secondary-button>
        </form>

        @if($livrosFiltrados)
            <p>Resultados da pesquisa para: "{{ $query }}"</p>
            <div class="grid lg:grid-cols-3 gap-8 mt-6">
                @foreach($livrosFiltrados as $livro)
                    <x-livro-card :livro="$livro" />
                @endforeach
            </div>
            <div class="mt-4">
                {{ $livrosFiltrados->links() }}
            </div>
        @else


            <p class="text-sm font-bold">Livros em destaque</p>
            <div class="grid lg:grid-cols-3 gap-8 mt-6">
                @if ($livrosSorteados->isEmpty())
                    <p class="text-gray-500">Nenhum livro cadastrado.</p>
                @else
                @foreach($livrosSorteados as $livro)
                    <x-livro-card :livro="$livro" />
                @endforeach
                @endif
            </div>
        @endif

</div>
</x-app-layout>