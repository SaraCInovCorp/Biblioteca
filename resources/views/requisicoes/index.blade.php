@php
    use Carbon\Carbon;
@endphp
<x-layout>
    <main>
        <h1 class="mr-auto font-bold text-lg">Requisições</h1>
        <div class="grid grid-cols-3 gap-2 mb-4">
            <div>
                <x-secondary-button as="a" 
                    href="{{ route('requisicoes.index', ['filtro' => 'ativas']) }}">
                    Requisições Ativas ({{ $indicators['requisicoes_ativas'] ?? '-' }})
                </x-secondary-button>
            </div>
            <div>
                <x-secondary-button as="a" 
                    href="{{ route('requisicoes.index', ['filtro' => '30dias']) }}">
                    Requisições nos últimos 30 dias ({{ $indicators['requisicoes_30dias'] ?? '-' }})
                </x-secondary-button>
            </div>
            <div>
                <x-secondary-button as="a" 
                    href="{{ route('requisicoes.index', ['filtro' => 'entregues_hoje']) }}">
                    Livros entregues Hoje ({{ $indicators['livros_entregues_hoje'] ?? '-' }})
                </x-secondary-button>
            </div>
        </div>
        <div>
            <h2 class="font-bold text-sm my-4">Requisições Ativas</h2>
            @foreach ($bookRequests->where('ativo', true) as $request)
                <div class="mb-4 border rounded p-3">
                    <p class="font-semibold"><a href="{{ route('requisicoes.show', $request) }}" class="text-blue-800 hover:underline">Requisição {{ $request->id }}</a></p>
                    <div class="pt-4 flex justify-between">
                        <div>
                            <ul>
                                <p class="font-semibold">Itens da Requisição</p>
                                @foreach ($request->items as $item)
                                    <li><a href="{{ route('livros.show', $item->livro) }}" class="text-blue-800 hover:underline">{{ $item->livro->titulo }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div>
                            <p><b>Data da Requisição:</b> {{ Carbon::parse($request->data_inicio)->format('d/m/Y') }}</p>
                            <p><b>Data da Entrega:</b> {{ Carbon::parse($request->data_fim)->format('d/m/Y') }}</p>
                            <p><b>Status:</b> {{ ucfirst($item->status) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <h2 class="font-bold mt-8 pb-4">Requisições Passadas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($bookRequests->where('ativo', false) as $request)
                <div class="border rounded p-3 shadow">
                    <p class="font-bold text-sm my-4">Requisição {{ $request->id }} - {{ Carbon::parse($request->data_inicio)->format('d/m/Y') }}</p>
                    <ul>
                        <p class="font-semibold">Itens da Requisição</p>
                        @foreach ($request->items as $item)
                            <li><a href="{{ route('requisicoes.show', $request) }}" class="text-blue-800 hover:underline">{{ $item->livro->titulo }}</a></li>
                        @endforeach
                    </ul>
                    <x-secondary-button as="a" href="{{ route('requisicoes.show', $request) }} " class="mt-4">
                        Ver Requisição
                    </x-secondary-button>
                </div>
            @endforeach
        </div>
        {{ $bookRequests->links() }}
    </main>
</x-layout>