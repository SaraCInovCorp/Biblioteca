@php
    use Carbon\Carbon;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lista de Requisições') }}
        </h2>
    </x-slot>
    <div class="flex-1 ">
        <div class="flex items-center mb-4 justify-end">
            @if(!empty($filtro) || request()->filled('search') || request()->filled('status') || request()->filled('data_inicio') || request()->filled('data_fim') || request()->filled('user_id'))
                <x-secondary-button 
                    type="button" 
                    class="mx-5"
                    onclick="window.location='{{ route('requisicoes.index') }}'">
                    Limpar Filtros
                </x-secondary-button>
            @endif
            <x-button type="button" onclick="window.location='{{ route('requisicoes.create') }}'">
                Nova Requisição
            </x-button>
        </div>

        {{-- Botões extras filtro rápido --}}
        <div class="grid grid-cols-3 gap-2 mb-4">
            <div>
                <x-secondary-button as="a" href="{{ route('requisicoes.index', ['filtro' => 'ativas']) }}"
                    :class="($filtro == 'ativas' ? 'bg-gray-300' : '')">
                    Requisições Ativas ({{ $indicators['requisicoes_ativas'] ?? '-' }})
                </x-secondary-button>
            </div>
            <div>
                <x-secondary-button as="a" href="{{ route('requisicoes.index', ['filtro' => '30dias']) }}"
                    :class="($filtro == '30dias' ? 'bg-gray-300' : '')">
                    Requisições nos últimos 30 dias ({{ $indicators['requisicoes_30dias'] ?? '-' }})
                </x-secondary-button>
            </div>
            <div>
                <x-secondary-button as="a" href="{{ route('requisicoes.index', ['filtro' => 'entregues_hoje']) }}"
                    :class="($filtro == 'entregues_hoje' ? 'bg-gray-300' : '')">
                    Livros entregues Hoje ({{ $indicators['livros_entregues_hoje'] ?? '-' }})
                </x-secondary-button>
            </div>
        </div>

        {{-- Formulário de filtro --}}
        <form method="GET" action="{{ route('requisicoes.index') }}" class="mb-6">
            {{-- Primeira linha: 4 colunas em telas grandes, 2 colunas em md, 1 coluna em sm --}}
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4 mb-4">
                <div class="flex flex-col justify-end h-full">
                    <x-label value="Pesquisar" for="search" />
                    <x-input 
                        type="text" 
                        id="search" 
                        name="search" 
                        placeholder="Livro ou autor" 
                        value="{{ request('search') ?? $search ?? '' }}" 
                        class="w-full" 
                    />
                </div>
                <div class="flex flex-col justify-end h-full">
                    <x-label value="Ativo" for="status" />
                    <x-select 
                        id="status" 
                        name="status" 
                        label="Ativo"
                        :options="['ativa' => 'Ativa', 'inativa' => 'Inativa']"
                        :selected="request('status') ?? $statusFiltro ?? ''"
                        class="w-full"
                    />
                </div>
                <div class="flex flex-col justify-end h-full">
                    <x-label value="Status" for="item_status" />
                    <x-select
                        id="item_status"
                        name="item_status"
                        label="Status"
                        :options="[
                            'cancelada' => 'Cancelada',
                            'realizada' => 'Realizada',
                            'entregue_ok' => 'Entregue OK',
                            'entregue_obs' => 'Entregue com Observação',
                            'nao_entregue' => 'Não Entregue',
                        ]"
                        :selected="request('item_status') ?? $itemStatusFiltro ?? ''"
                        class="w-full"
                    />
                </div>
                <div class="flex flex-col justify-end h-full">
                    <x-label value="Data Início" for="data_inicio" />
                    <x-input 
                        type="date"
                        id="data_inicio"
                        name="data_inicio"
                        value="{{ request('data_inicio') ?? $dataInicioFiltro ?? '' }}"
                        class="w-full"
                    />
                </div>
                <div class="flex flex-col justify-end h-full">
                    <x-label value="Previsão Entrega" for="data_fim" />
                    <x-input 
                        type="date"
                        id="data_fim"
                        name="data_fim"
                        value="{{ request('data_fim') ?? $dataFimFiltro ?? '' }}"
                        class="w-full"
                    />
                </div>
                <div class="flex flex-col justify-end h-full">
                    <x-label value="Entrega real" for="data_real_entrega" />
                    <x-input 
                        type="date"
                        id="data_real_entrega"
                        name="data_real_entrega"
                        value="{{ request('data_real_entrega') ?? $dataRealEntregaFiltro ?? '' }}"
                        class="w-full"
                    />
                </div>
            </div>
            {{-- Segunda linha: Usuário + botões no fim --}}
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4 items-end">
                {{-- Usuário --}}
                <div class="flex flex-col justify-end h-full">
                    @if(auth()->user()->role === 'admin')
                        <x-label value="Usuário" for="user_id" />
                        @if(isset($users) && $users->isNotEmpty())
                            <x-select
                                id="user_id"
                                name="user_id"
                                label="Usuário"
                                :options="$users->pluck('name', 'id')->toArray()"
                                :selected="request('user_id') ?? $userIdFiltro ?? ''"
                                class="w-full"
                            />
                        @endif
                    @endif
                </div>
                {{-- Botões alinhados à direita na tela grande, centralizados em mobile --}}
                <div class="md:col-span-2 lg:col-span-2 flex justify-end space-x-3">
                    <x-secondary-button as="a" href="{{ route('requisicoes.index') }}" class="min-w-[120px]">Limpar</x-secondary-button>
                    <x-button type="submit" class="min-w-[120px]">Filtrar</x-button>
                </div>
            </div>
        </form>

        {{-- Listagem --}}
        @if(!$bookRequests->isEmpty())
           
            <!-- <p class="text-gray-600 italic mb-6">Nenhuma requisição encontrada para o filtro selecionado.</p> -->
        
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($bookRequests as $request)
                    <div class="border rounded p-3 shadow bg-white">
                        <p class="font-bold text-sm my-2">
                            Requisição {{ $request->id }} - {{ Carbon::parse($request->data_inicio)->format('d/m/Y') }}
                        </p>
                        <p class="mb-4 font-semibold {{ $request->ativo ? 'text-green-600' : 'text-red-600' }}">
                            Status da Requisição: {{ $request->ativo ? 'Ativa' : 'Inativa' }}
                        </p>
                        <ul>
                            <p class="font-semibold">Itens da Requisição</p>
                            @foreach($request->items as $item)
                                <li class="flex justify-between items-center">
                                    <a href="{{ route('livros.show', $item->livro) }}" class="text-blue-800 hover:underline">
                                        {{ $item->livro->titulo }}
                                    </a>
                                    <span class="ml-4 text-sm font-medium 
                                        {{ in_array($item->status, ['realizada', 'nao_entregue']) ? 'text-green-600' : 'text-red-600' }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <x-secondary-button as="a" href="{{ route('requisicoes.show', $request) }}" class="mt-4">
                            Ver Requisição
                        </x-secondary-button>
                    </div>
                @endforeach
            </div>

            {{ $bookRequests->links() }}
            

        @else
            @if($ativas->isEmpty() && $passadas->isEmpty())
                <p class="text-gray-600 italic mb-6">Nenhuma requisição encontrada.</p>
            @else
                @if($ativas->isNotEmpty())
                    <h2 class="font-bold text-sm my-4">Requisições Ativas</h2>
                    @foreach($ativas as $request)
                        <div class="mb-4 border rounded p-3 bg-white">
                            <p class="font-semibold"><a href="{{ route('requisicoes.show', $request) }}" class="text-blue-800 hover:underline">Requisição {{ $request->id }}</a></p>
                            <div class="pt-4 flex justify-between">
                                <div>
                                    <ul>
                                        <p class="font-semibold">Itens da Requisição</p>
                                        @foreach ($request->items as $item)
                                            <li>
                                                <p><a href="{{ route('livros.show', $item->livro) }}" class="text-blue-800 hover:underline">{{ $item->livro->titulo }}</a></p>
                                                <p><b>Status:</b> {{ ucfirst($item->status) }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div>
                                    <p><b>Data da Requisição:</b> {{ Carbon::parse($request->data_inicio)->format('d/m/Y') }}</p>
                                    <p><b>Data da Entrega:</b> {{ Carbon::parse($request->data_fim)->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if($passadas->isNotEmpty())
                    <h2 class="font-bold mt-8 pb-4">Requisições Passadas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($passadas as $request)
                            <div class="border rounded p-3 bg-white shadow">
                                <p class="font-bold text-sm my-2">Requisição {{ $request->id }} - {{ Carbon::parse($request->data_inicio)->format('d/m/Y') }}</p>
                                <p class="mb-4 font-semibold {{ $request->ativo ? 'text-green-600' : 'text-red-600' }}">
                                    Status da Requisição: {{ $request->ativo ? 'Ativa' : 'Inativa' }}
                                </p>
                                <ul>
                                    <p class="font-semibold">Itens da Requisição</p>
                                    @foreach($request->items as $item)
                                        <li class="flex justify-between items-center">
                                            <a href="{{ route('livros.show', $item->livro) }}" class="text-blue-800 hover:underline">
                                                {{ $item->livro->titulo }}
                                            </a>
                                            <span class="ml-4 text-sm font-medium 
                                                {{ in_array($item->status, ['realizada', 'nao_entregue']) ? 'text-green-600' : 'text-red-600' }}">
                                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                                <x-secondary-button as="a" href="{{ route('requisicoes.show', $request) }}" class="mt-4">
                                    Ver Requisição
                                </x-secondary-button>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        @endif
    </main>
</x-layout>
