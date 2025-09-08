@php
    use Illuminate\Support\Str;
@endphp
<x-layout>
    <main>
        <div class="py-4 w-full flex justify-between items-center">
            <div>
                <p class="mr-auto font-bold text-lg">Editoras</p>
                <p>Total de editoras: {{ $editoras->total() }}</p>
            </div>
            <div>
                @if(auth()->check() && auth()->user()->isAdmin())
                <x-secondary-button as="a" href="{{ route('editoras.create') }}">Nova Editora</x-secondary-button>
                @endif
            </div>
        </div>
        <div class="w-full">
        <form method="GET" action="{{ route('editoras.index') }}" class="mb-6 w-full flex gap-2 items-center">
            <x-input 
                type="text" 
                name="query" 
                placeholder="Buscar editora..." 
                value="{{ $query ?? '' }}" 
                class="flex-grow min-w-0"
            />
            <x-button type="submit" class="min-w-[90px]">Buscar</x-button>
            <x-secondary-button as="a" href="{{ route('editoras.index') }}" class="whitespace-nowrap min-w-[90px]">Limpar Filtros</x-secondary-button>
        </form>
        </div>
        @if ($editoras->isEmpty())
            <p class="text-gray-500">Nenhuma editora cadastrada.</p>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($editoras as $editora)
            <div class="flex items-center gap-4 p-4 rounded border shadow-sm bg-white">
                <img src="{{ Str::startsWith($editora->logo_url, ['http://','https://']) ? $editora->logo_url : asset('storage/'.$editora->logo_url) }}" alt="{{ $editora->nome }}" class="w-16 h-16 rounded object-cover" />
                <div class="flex-grow">
                <p class="font-semibold">{{ $editora->nome }}</p>
                </div>
                <x-secondary-button as="a" href="{{ route('editoras.show', $editora) }}">Ver detalhes</x-secondary-button>
            </div>
            @endforeach
            </div>
            <div class="mt-6">
                <div class="mb-4 flex justify-end gap-2">
                    <x-secondary-button as="a" href="{{ route('editoras.export.excel', request()->query()) }}">
                        Exportar Excel
                    </x-secondary-button>
                    <x-secondary-button as="a" href="{{ route('editoras.export.pdf', request()->query()) }}">
                        Exportar PDF
                    </x-secondary-button>
                </div>

                {{ $editoras->links() }}
            </div>
        @endif
    </main>
</x-layout>