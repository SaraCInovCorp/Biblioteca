@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
    $primeiroItem = $bookRequest->items->first();
    $entregaReal = $primeiroItem ? $primeiroItem->data_real_entrega : null;
    $user = $bookRequest->user;
    $isAdmin = auth()->user()->role === 'admin';
    
@endphp

<x-layout>
    <main>
        <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4">
            {{-- Coluna 1: Dados do Usuário --}}
            <div class="col-span-1">
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
                            <p><strong>Nome:</strong> {{ $user->name }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>Perfil:</strong> {{ ucfirst($user->role) }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Coluna 2: Dados da Requisição --}}
            <div class="col-span-1">
                <div class="border rounded p-4 h-full bg-white shadow">
                    <p class="font-bold mb-4">Requisição {{ $bookRequest->id }} - {{ Carbon::parse($bookRequest->data_inicio)->format('d/m/Y') }}</p>
                    <p><b>Data da Requisição:</b> {{ Carbon::parse($bookRequest->data_inicio)->format('d/m/Y') }}</p>
                    <p><b>Data Prevista da Entrega:</b> {{ Carbon::parse($bookRequest->data_fim)->format('d/m/Y') }}</p>
                    <p><b>Data Entrega:</b> {{ $entregaReal ? Carbon::parse($entregaReal)->format('d/m/Y') : 'Não entregue' }}</p>
                    <p><b>Dias corridos:</b> {{ $primeiroItem && $primeiroItem->dias_decorridos ? $primeiroItem->dias_decorridos : '-' }}</p>
                    <p><b>Status:</b> {{ $primeiroItem ? ucfirst($primeiroItem->status) : '-' }}</p>

                    @if (!empty($bookRequest->notas))
                        <div class="bg-gray-100 p-3 mt-4 rounded">
                            <p><strong>Nota:</strong></p>
                            <p>{{ $bookRequest->notas }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Coluna 3: Livros Requisitados --}}
            <div class="col-span-1">
                <h3 class="font-semibold mb-2">Livros Requisitados</h3>
                <ul class="space-y-3 max-h-[70vh] overflow-auto">
                    @foreach ($bookRequest->items as $item)
                        <li class="flex items-center gap-3 p-3 bg-white rounded shadow">
                            @if(!empty($item->livro->capa_url))
                                <img src="{{ \Illuminate\Support\Str::startsWith($item->livro->capa_url, ['http://','https://']) ? $item->livro->capa_url : asset('storage/'.$item->livro->capa_url) }}" alt="Capa do livro {{ $item->livro->capa_url }}" style="width: 50px; height: auto;">
                            @else
                                <div class="w-12 h-16 bg-gray-300 rounded border flex items-center justify-center text-gray-700 text-xs">Sem capa</div>
                            @endif
                            <div>
                                <a href="{{ route('livros.show', $item->livro) }}" class="font-medium text-blue-600 hover:underline">
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
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="mt-6 flex gap-3 justify-end">
                <x-secondary-button onclick="window.history.back()">
                    Voltar
                </x-secondary-button>
                @if($isAdmin)
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
