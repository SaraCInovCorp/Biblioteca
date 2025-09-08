@php
    use Illuminate\Support\Str;
@endphp
<x-layout>
    <main>
        <div class="py-4 w-full flex justify-between items-center">
            <div>
                <p class="mr-auto font-bold text-lg">Autores</p>
                <p>Total de Autores: {{ $autores->total() }}</p>
            </div>
            <div>
                @if(auth()->check() && auth()->user()->isAdmin())
                <x-secondary-button as="a" href="{{ route('autores.create') }}">Novo Autor</x-secondary-button>
                @endif
            </div>
        </div>
        <div class="w-full">
            <form method="GET" action="{{ route('autores.index') }}" class="mb-6 w-full flex gap-2 items-center">
                <x-input 
                    type="text" 
                    name="query" 
                    placeholder="Buscar autor..." 
                    value="{{ $query ?? '' }}" 
                    class="flex-grow min-w-0"
                />
                <x-button type="submit" class="min-w-[90px]">Buscar</x-button>
                <x-secondary-button as="a" href="{{ route('autores.index') }}" class="ml-2 px-4 py-2 border rounded text-gray-700 hover:bg-gray-200 whitespace-nowrap min-w-[90px]">
                    Limpar Filtros
                </x-secondary-button>
            </form>
        </div>
        @if ($autores->isEmpty())
            <p class="text-gray-500">Nenhum autor cadastrado.</p>
        @else
       <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
       @foreach ($autores as $autor)
            <div class="flex items-center gap-4 p-4 rounded border shadow-sm bg-white">
                <img src="{{ Str::startsWith($autor->foto_url, ['http://','https://']) ? $autor->foto_url : asset('storage/'.$autor->foto_url) }}" alt="{{ $autor->nome }}" class="w-16 h-16 rounded object-cover" />
                <div class="flex-grow">
                <p class="font-semibold">{{ $autor->nome }}</p>
                </div>
                <x-secondary-button as="a" href="{{ route('autores.show', $autor) }}">Ver detalhes</x-secondary-button>
            </div>
            @endforeach
            </div>
            <div class="mt-6">
                <div class="mb-4 flex justify-end gap-2">
                    <x-secondary-button as="a" href="{{ route('autores.export.excel', request()->query()) }}">
                        Exportar Excel
                    </x-secondary-button>
                    <x-secondary-button as="a" href="{{ route('autores.export.pdf', request()->query()) }}">
                        Exportar PDF
                    </x-secondary-button>
                </div>
                {{ $autores->links() }}
            </div>
        @endif
    </main>
</x-layout>