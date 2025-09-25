@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Livros disponíveis com interessados na lista de espera
        </h2>
    </x-slot>

    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse ($livros as $livro)
            <x-card-generic
                :imageUrl="$livro->capa_url"
                :title="$livro->titulo"
                :description="'Interessados: ' . $livro->waitingList->count()"
                buttonText="Ver detalhes"
                :buttonUrl="route('livros.show', $livro->id)"
                class="w-full"
            >
                <p class="text-sm mt-2">Emails:</p>
                <ul class="text-xs text-gray-700 list-disc list-inside">
                    @foreach ($livro->waitingList as $inscricao)
                        <li>{{ $inscricao->user->email }}</li>
                    @endforeach
                </ul>
            </x-card-generic>
        @empty
            <p class="mt-6 text-gray-600">Nenhum livro disponível com interessados atualmente.</p>
        @endforelse
    </div>
</x-app-layout>