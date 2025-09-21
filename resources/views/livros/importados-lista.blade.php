<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Livros Importados</h2>
    </x-slot>

    <div class="flex-1">
        @if(session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($importacoes->isEmpty())
            <p class="text-gray-500">Nenhuma importação registrada.</p>
        @else
            <div class="mb-4 flex gap-2 flex-wrap">
                @foreach($importacoes as $imp)
                    <a href="{{ route('livros.importados.list', ['highlight' => $imp->id]) }}" 
                    class="px-4 py-2 rounded {{ $imp->id === $ultimaImportacao->id ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                        Importação #{{ $imp->id }} - {{ $imp->imported_at->format('d/m/Y H:i') }}
                    </a>
                @endforeach
            </div>

            <div class="mb-4">
                <a href="{{ route('importacoes.export.excel', ['id' => $ultimaImportacao->id]) }}" class="btn btn-success">Exportar Excel</a>
                <a href="{{ route('importacoes.export.pdf', ['id' => $ultimaImportacao->id]) }}" class="btn btn-danger">Exportar PDF</a>

            </div>

            <h3 class="mb-4 font-semibold">Livros da Importação Selecionada</h3>

            <div id="livros-importados-lista" class="grid lg:grid-cols-3 gap-8">
                @foreach($livros as $livro)
                    <x-livro-card :livro="$livro" />
                @endforeach
            </div>

            <div class="mt-6">
                {{ $importacoes->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
