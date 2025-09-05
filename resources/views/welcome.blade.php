<x-layout>
    <main class="flex-grow">
        <form method="GET" action="{{ route('welcome') }}" class="mb-6">
            <x-label for="search">Pesquise seu livro, autor ou editora:</x-label>
            <x-input type="text" name="query" id="search" value="{{ $query }}" class="w-full" placeholder="Digite para pesquisar..."/>
            <x-button type="submit" class="mt-5">Buscar</x-button>
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
                @foreach($livrosSorteados as $livro)
                    <x-livro-card :livro="$livro" />
                @endforeach
            </div>
        @endif

    </main>
</x-layout>