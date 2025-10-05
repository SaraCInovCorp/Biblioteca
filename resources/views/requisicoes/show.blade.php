@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
    $primeiroItem = $bookRequest->items->first();
    $entregaReal = $primeiroItem ? $primeiroItem->data_real_entrega : null;
    $user = $bookRequest->user;
    $isAdmin = auth()->user()->role === 'admin';
    
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Requisição') }} {{ $bookRequest->id }}
        </h2>
    </x-slot>
    <div class="flex-1">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
            {{-- Bloco 1: Dados do Usuário --}}
            @if($isAdmin)
                <div class="border rounded p-4 bg-white shadow">
                    @if($user)
                        <div class="flex items-center gap-4">
                            @if($user->profile_photo_path)
                                <img src="{{ Str::startsWith($user->profile_photo_path, ['http://', 'https://']) ? $user->profile_photo_path : asset('storage/'.$user->profile_photo_path) }}" 
                                     alt="Foto de perfil de {{ $user->name }}" 
                                     class="w-16 h-16 rounded-full object-cover">
                            @else
                                <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm">Sem foto</div>
                            @endif
                            <div>
                                <h3 class="font-semibold mb-2">Dados do Usuário</h3>
                                <p><strong>Nome:</strong>
                                    <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:underline">
                                        {{ $user->name }}
                                    </a>
                                </p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                <p><strong>Perfil:</strong> {{ ucfirst($user->role) }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Bloco 2: Dados da Requisição --}}
            <div class="border rounded p-4 bg-white shadow">
                <p><b>Data da Requisição:</b> {{ Carbon::parse($bookRequest->data_inicio)->format('d/m/Y') }}</p>
                <p><b>Data Prevista da Entrega:</b> {{ Carbon::parse($bookRequest->data_fim)->format('d/m/Y') }}</p>
                @if($bookRequest->ativo)
                    <p class="font-bold text-green-600">Requisição Ativa</p>
                @else
                    <p class="font-bold text-red-600">Requisição Inativa</p>
                @endif
                @if (!empty($bookRequest->notas))
                    <div class="bg-gray-100 p-3 mt-4 rounded">
                        <p><strong>Nota:</strong></p>
                        <p>{{ $bookRequest->notas }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Bloco 3: Listagem dos livros requisitados (abaixo, ocupando toda a largura) --}}
        <div class="max-w-6xl mx-auto mt-8">
            <h3 class="font-semibold mb-4">Livros Requisitados</h3>
            <div class="space-y-4">
                @foreach ($bookRequest->items as $item)
                    <div class="flex gap-4 p-4 bg-white border rounded shadow items-center">
                        {{-- Capa --}}
                        @if(!empty($item->livro->capa_url))
                            <img src="{{ \Illuminate\Support\Str::startsWith($item->livro->capa_url, ['http://','https://']) ? $item->livro->capa_url : asset('storage/'.$item->livro->capa_url) }}" alt="Capa do livro {{ $item->livro->titulo }}" class="w-24 h-32 object-contain rounded">
                        @else
                            <div class="w-24 h-32 bg-gray-300 rounded border flex items-center justify-center text-gray-700 text-xs">Sem capa</div>
                        @endif
                        {{-- Dados do livro --}}
                        <div class="flex-1">
                            <a href="{{ route('livros.show', $item->livro) }}" class="block font-medium text-blue-600 hover:underline">
                                {{ $item->livro->titulo }}
                            </a>
                            <div class="text-sm text-gray-600">
                                @if($item->livro->autores->isNotEmpty())
                                    {{ $item->livro->autores->pluck('nome')->join(', ') }}
                                @endif
                                <br>
                                {{ $item->livro->editora->nome ?? '' }}
                            </div>
                        </div>
                        {{-- Datas/status/obs --}}
                        <div class="text-sm min-w-[180px]">
                            <div><b>Data Entrega:</b> {{ $item->data_real_entrega ? Carbon::parse($item->data_real_entrega)->format('d/m/Y') : 'Não entregue' }}</div>
                            <div><b>Dias corridos:</b> {{ $item->dias_decorridos ?? '-' }}</div>
                            <div><b>Status:</b> {{ ucfirst($item->status) }}</div>
                            <div><b>Obs:</b> {{ $item->obs }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Botões de ação --}}
        <div class="mt-6 flex gap-3 justify-end">
            <x-secondary-button as="a" href="{{ route('requisicoes.index') }}" class="hover:bg-red-700 hover:text-white ">
                Voltar
            </x-secondary-button>
            @if($bookRequest->ativo && now()->lt($bookRequest->data_inicio))
                <form action="{{ route('requisicoes.destroy', $bookRequest) }}" method="POST" onsubmit="return confirm('Confirma o cancelamento de toda a requisição?');">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" style="danger">
                        Cancelar Requisição
                    </x-button>
                </form>
            @endif
            @if($isAdmin || auth()->id() === $bookRequest->user_id)
                <x-button
                    onclick="location.href='{{ route('requisicoes.edit', $bookRequest) }}'"
                    type="button"
                >
                    Editar
                </x-button>
            @endif
        </div>
    </div>
</x-app-layout>
