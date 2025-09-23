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
    <div class="flex-1 ">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 p-4">
            {{-- Coluna 1: Dados do Usuário --}}
            @if($isAdmin)
            <div>
                @if($isAdmin && $user)
                    <div class="border rounded p-4 h-full flex items-center gap-4 bg-white shadow">
                        @if($user->profile_photo_path)
                            <img src="{{ Str::startsWith($user->profile_photo_path, ['http://', 'https://']) ? $user->profile_photo_path : asset('storage/'.$user->profile_photo_path) }}" 
                                alt="Foto de perfil de {{ $user->name }}" 
                                class="w-16 h-16 rounded-full object-cover">
                        @else
                            <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm">
                                Sem foto
                            </div>
                        @endif
                        <div>
                            <h3 class="font-semibold mb-2">Dados do Usuário</h3>
                            <p><strong>Nome:</strong> 
                            <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:underline">
                                {{ $user->name }}
                            </a></p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>Perfil:</strong> {{ ucfirst($user->role) }}</p>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            {{-- Coluna 2: Dados da Requisição --}}
            <div>
                <div class="border rounded p-4 h-full bg-white shadow">
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

            {{-- Coluna 3: Livros Requisitados --}}
            <div class="w-full mt-4">
                <h3 class="font-semibold mb-2">Livros Requisitados</h3>
                <ul class="space-y-3 w-full">
                    @foreach ($bookRequest->items as $item)
                        <li class="w-full grid grid-cols-1 md:grid-cols-[8rem_1fr_12rem] gap-4 md:gap-6 p-3 bg-white rounded shadow items-center">
                            {{-- Capa --}}
                            @if(!empty($item->livro->capa_url))
                                <div>
                                    <img src="{{ \Illuminate\Support\Str::startsWith($item->livro->capa_url, ['http://','https://']) ? $item->livro->capa_url : asset('storage/'.$item->livro->capa_url) }}" alt="Capa do livro {{ $item->livro->titulo }}" class="w-24 h-auto rounded">
                                </div>
                                    @else
                                <div class="w-24 h-32 bg-gray-300 rounded border flex items-center justify-center text-gray-700 text-xs">Sem capa</div>
                            @endif

                            {{-- Dados do livro --}}
                            <div>
                                <a href="{{ route('livros.show', $item->livro) }}" class="font-medium text-blue-600 hover:underline">
                                    {{ $item->livro->titulo }}
                                </a>
                                <div class="text-sm text-gray-600 mt-1">
                                    @if($item->livro->autores->isNotEmpty())
                                        {{ $item->livro->autores->pluck('nome')->join(', ') }}
                                    @endif
                                    <br>
                                    {{ $item->livro->editora->nome ?? '' }}
                                </div>
                            </div>

                            {{-- Dados do item --}}
                            <div class="text-sm text-gray-700 space-y-1">
                                <p><strong>Data Entrega:</strong> {{ $item->data_real_entrega ? \Carbon\Carbon::parse($item->data_real_entrega)->format('d/m/Y') : 'Não entregue' }}</p>
                                <p><strong>Dias corridos:</strong> {{ $item->dias_decorridos ?? '-' }}</p>
                                <p><strong>Status:</strong> {{ ucfirst($item->status) }}</p>
                                <p><strong>Obs:</strong> {{ ucfirst($item->obs) }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

            </div>

        </div>
        <div class="mt-6 flex gap-3 justify-end">
                <x-secondary-button onclick="window.history.back()">
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
    </main>
</x-layout>
