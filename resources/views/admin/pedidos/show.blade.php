@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $user = $pedido->user;
    $isAdmin = auth()->user()->role === 'admin';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detalhes do Pedido #{{ $pedido->id }}
        </h2>
    </x-slot>

    <div class="flex-1">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">

            {{-- Coluna 1: Dados do Usuário --}}
            @if($isAdmin)
                <div>
                    @if($user)
                        <div class="border rounded p-4 bg-white shadow">
                            @if($user->profile_photo_path)
                                <img src="{{ Str::startsWith($user->profile_photo_path, ['http://', 'https://']) ? $user->profile_photo_path : asset('storage/'.$user->profile_photo_path) }}" 
                                     alt="Foto de perfil de {{ $user->name }}" 
                                     class="w-16 h-16 rounded-full object-cover" />
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
                                    </a>
                                </p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                <p><strong>Perfil:</strong> {{ ucfirst($user->role) }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Coluna 2: Dados do Pedido --}}
            <div>
                <div class="border rounded p-4 bg-white shadow">
                    <p><strong>Data do Pedido:</strong> {{ Carbon::parse($pedido->created_at)->format('d/m/Y H:i') }}</p>
                    <p><strong>Status do Pedido:</strong> {{ ucfirst($pedido->status) }}</p>
                    <p><strong>Status do Pagamento:</strong> {{ ucfirst($pedido->payment_status) }}</p>
                    <p><strong>Total:</strong> €{{ number_format($pedido->total, 2, ',', '.') }}</p>

                    <h3 class="mt-4 font-semibold">Endereço de entrega</h3>
                    <p>
                        {{ $pedido->endereco->logradouro }}, {{ $pedido->endereco->numero }}<br>
                        {{ $pedido->endereco->localidade }} - {{ $pedido->endereco->distrito }}<br>
                        {{ $pedido->endereco->codigo_postal }}<br>
                        {{ $pedido->endereco->pais }}
                    </p>

                    {{-- Botões de Aprovar / Reprovar solicitação de cancelamento --}}
                    @if($pedido->cancelamento_solicitado)
                        <div class="mt-6 flex gap-3 justify-start">
                            <form method="POST" action="{{ route('admin.pedidos.aprovar-cancelamento', $pedido) }}" onsubmit="return confirm('Aprova o cancelamento e executa o reembolso?');">
                                @csrf
                                <x-button type="submit" class="bg-green-800 hover:bg-green-800/50 hover:text-black px-2 py-4">Aprovar Cancelamento</x-button>
                            </form>
                            <form method="POST" action="{{ route('admin.pedidos.rejeitar-cancelamento', $pedido) }}" onsubmit="return confirm('Rejeita o cancelamento?');">
                                @csrf
                                <x-button type="submit" class="bg-orange-800 hover:bg-orange-800/50 hover:text-black px-2 py-4">Rejeitar Cancelamento</x-button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Coluna 3: Itens do Pedido --}}
            <div class="md:col-span-2">
                <h3 class="font-semibold mb-2">Itens do Pedido</h3>
                <div class="space-y-4">
                    @foreach($pedido->items as $item)
                        <div class="flex gap-4 p-4 bg-white border rounded shadow items-center">
                            {{-- Capa --}}
                            @if(!empty($item->livro->capa_url))
                                <img src="{{ Str::startsWith($item->livro->capa_url, ['http://','https://']) ? $item->livro->capa_url : asset('storage/'.$item->livro->capa_url) }}" alt="Capa do livro {{ $item->livro->titulo }}" class="w-24 h-auto rounded" />
                            @else
                                <div class="w-24 h-32 bg-gray-300 rounded border flex items-center justify-center text-gray-700 text-xs">Sem capa</div>
                            @endif

                            {{-- Dados do livro --}}
                            <div class="flex-1">
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

                            {{-- Dados do item --}}
                            <div class="text-sm min-w-[160px]">
                                <div><b>Quantidade:</b> {{ $item->quantidade }}</div>
                                <div><b>Preço Unitário:</b> €{{ number_format($item->preco_unitario, 2, ',', '.') }}</div>
                                <div><b>Subtotal:</b> €{{ number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') }}</div>
                                <div><b>Status:</b> {{ ucfirst($item->status) }}</div>
                                <div><b>Observações:</b> {{ ucfirst($item->obs) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
        <div class="flex justify-end items-center gap-6">
            <x-secondary-button as="a" href="{{ route('admin.pedidos.index') }}">
                Voltar para Pedidos
            </x-secondary-button>
            @if($isAdmin && $pedido->status !== 'cancelado')
                <form action="{{ route('admin.pedidos.cancelar', $pedido) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar este pedido?');">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" class="bg-red-700 hover:bg-red-700/70">Cancelar Pedido (Admin)</x-button>
                </form>
            @endif
        </div>
        </div>

    </div>
</x-app-layout>
