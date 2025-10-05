@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
    $user = $pedido->user; // usuário dono do pedido
    $isAdmin = auth()->user()->role === 'admin';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detalhe do Pedido #{{ $pedido->id }}
        </h2>
    </x-slot>

    <div class="flex-1">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4">

            {{-- Coluna 1: Dados do Usuário (só exibe para admin) --}}
            @if($isAdmin)
                <div>
                    <div class="border rounded p-4 h-full flex items-center gap-4 bg-white shadow">
                        @if($user && $user->profile_photo_path)
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
                </div>
            </div>

            {{-- Coluna 3: Itens do Pedido --}}
            <div>
                <h3 class="font-semibold mb-4">Itens do Pedido</h3>
                <ul class="space-y-4">
                    @foreach ($pedido->items as $item)
                        <li class="border rounded p-3 bg-white shadow flex gap-4 items-center">
                            @if(!empty($item->livro->capa_url))
                                <img src="{{ Str::startsWith($item->livro->capa_url, ['http://','https://']) ? $item->livro->capa_url : asset('storage/'.$item->livro->capa_url) }}" 
                                     alt="Capa do livro {{ $item->livro->titulo }}" 
                                     class="w-24 rounded" />
                            @else
                                <div class="w-24 h-32 bg-gray-300 rounded border flex items-center justify-center text-gray-600 text-xs">Sem Capa</div>
                            @endif
                            <div class="flex-1">
                                <a href="{{ route('livros.show', $item->livro) }}" class="text-blue-600 font-medium hover:underline">
                                    {{ $item->livro->titulo }}
                                </a>
                                <p class="text-sm text-gray-600 mt-1">Quantidade: {{ $item->quantidade }}</p>
                                <p class="text-sm text-gray-600">Preço unitário: €{{ number_format($item->preco_unitario, 2, ',', '.') }}</p>
                                <p class="text-sm font-semibold">Subtotal: €{{ number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
        
        
            <div class="flex justify-end items-center gap-6">
    <x-secondary-button as="a" href="{{ route('pedidos.meus') }}">
        Voltar para Meus Pedidos
    </x-secondary-button>

    @if($pedido->status === 'pendente')
        <form action="{{ route('pedidos.cancelar', $pedido) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar o pedido?');">
            @csrf
            @method('DELETE')
            <x-button type="submit">Cancelar Pedido</x-button>
        </form>
    @elseif(!in_array($pedido->status, ['cancelado']) && $pedido->cancelamento_solicitado)
        <span class="text-yellow-600 font-semibold">
            Solicitação de cancelamento pendente de aprovação administrativa.
        </span>
    @elseif(!in_array($pedido->status, ['cancelado', 'pendente']) && !$pedido->cancelamento_solicitado)
        <form action="{{ route('pedidos.solicitar-cancelamento', $pedido) }}" method="POST" onsubmit="return confirm('Deseja solicitar o cancelamento deste pedido?');">
            @csrf
            <x-button type="submit" class="bg-orange-700 hover:bg-orange-700/50 hover:text-black">
                Solicitar Cancelamento
            </x-button>
        </form>
    @endif
</div>

    </div>
</x-app-layout>
