@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhe do ') }} {{ $livro->titulo }}
        </h2>
    </x-slot>
    <div class="flex-1 ">
        <div class="flex flex-row items-start justify-center gap-6 mt-6 max-w-5xl mx-auto">
            <div class="flex-shrink-0">
                <img src="{{ Str::startsWith($livro->capa_url, ['http://','https://']) ? $livro->capa_url : asset('storage/'.$livro->capa_url) }}" 
                    alt="Capa do livro {{ $livro->titulo }}" 
                    style="max-width: 300px; height: auto;">
                <x-button type="button" onclick="window.history.back()" class="mt-4 w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                    Voltar
                </x-button>
                @if(auth()->check() && $livro->status === 'disponivel')
                    <x-button
                        type="button"
                        class="mt-4 w-full bg-green-900 hover:bg-green-400 text-white font-semibold py-2 px-4 rounded"
                        onclick="adicionarLivroEIrCriar({{ $livro->id }}, '{{ addslashes($livro->titulo) }}', '{{ addslashes($livro->autor ?? '') }}')"
                    >
                        Requisitar este livro
                    </x-button>
                @endif
                @if(auth()->check() && auth()->user()->isAdmin())
                <x-button type="button" onclick="window.location='{{ route('livros.edit', $livro->id) }}'" class="mt-4 w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded">
                    Editar
                </x-button>
                <form action="{{ route('livros.destroy', $livro->id) }}" method="POST" onsubmit="return confirm('Confirma alteração do status do livro?');">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" class="mt-4 w-full bg-red-600 hover:bg-red-300 text-white font-semibold py-2 px-4 rounded">
                        Alternar Disponibilidade
                    </x-button>
                </form>

                @endif
            </div>
            <div class="max-w-3xl p-6 bg-white rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-4">{{ $livro->titulo }}</h2>
                <p class="mt-1"><span class="font-semibold">Bibliografia:</span><span class="text-sm italic block mt-1"> <br/>{{ $livro->bibliografia }}</span></p>
                <p class="mt-4"><span class="font-semibold">Editora:</span> <br/>{{ $livro->editora->nome ?? 'Editora não informada' }}</p>
                <p class="mt-2"><span class="font-semibold">Autor(es):</span> <br/>{{ $livro->autores->pluck('nome')->join(', ') ?? 'Autor não informado' }}</p>
                <p class="mt-2">
                    <span class="font-semibold">Status:</span> 
                    @if($livro->status === 'disponivel')
                        <span class="text-green-600 uppercase">Disponível</span>
                    @elseif($livro->status === 'requisitado')
                        <span class="text-yellow-600 uppercase">Requisitado</span>
                    @else
                        <span class="text-red-600 uppercase">{{ ucfirst($livro->status) }}</span>
                    @endif
                </p>

                <p class="mt-2"><span class="font-semibold">ISBN:</span> <br/>{{ $livro->isbn }}</p>
                <h3 class="font-semibold mt-6 mb-2">Histórico de Requisições deste Livro</h3>
                @if($historico->isEmpty())
                    <p class="text-gray-600 italic">Nenhuma requisição para este livro.</p>
                @else
                    <ul class="space-y-3">
                        @foreach($historico as $item)
                            <li class="bg-gray-50 p-3 rounded shadow">
                                <p><strong>
                                    <a href="{{ route('requisicoes.show', $item->bookRequest) }}" class="text-blue-600 hover:underline">
                                        Requisição #{{ $item->bookRequest->id }}
                                    </a>
                                </strong> - {{ $item->bookRequest->user->name ?? 'Usuário deletado' }}</p>
                                <p>Data Início: {{ Carbon::parse($item->bookRequest->data_inicio)->format('d/m/Y') }}</p>
                                <p>Status do item: {{ ucfirst($item->status) }}</p>
                                <p>Data Real de Entrega: {{ $item->data_real_entrega ? \Carbon\Carbon::parse($item->data_real_entrega)->format('d/m/Y') : 'Não entregue' }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <script>
            function adicionarLivroEIrCriar(id, titulo, autor) {
            console.log({id, titulo, autor}); 

            fetch("{{ route('requisicoes.session.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    livro: {
                        id: id,
                        titulo: titulo,
                        autor: autor
                    }
                }),
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => { throw new Error(data.error || 'Erro ao adicionar livro'); });
                }
                return response.json();
            })
            .then(data => {
                console.log('Livro adicionado:', data);
                window.location.href = "{{ route('requisicoes.create') }}";
            })
            .catch(error => {
                alert(error.message);
            });
        }

        </script>
    </div>
</x-app-layout>