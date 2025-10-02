<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Carrinho de Compras') }}
        </h2>
    </x-slot>

    <div class="flex-1 px-4 py-6 max-w-7xl mx-auto">
        @if ($itens->isEmpty())
            <p class="text-gray-500">Seu carrinho está vazio.</p>
        @else
            {{-- Cabeçalho visível só em sm+ --}}
            <div class="hidden sm:flex border-t border-gray-300 bg-gray-100 font-semibold text-gray-700 items-center">
                <div class="flex-1 min-w-[180px] py-2">Livro</div>
                <div class="w-28 py-2 text-center">Quantidade</div>
                <div class="w-32 py-2 text-center">Preço Unitário</div>
                <div class="w-32 py-2 text-center">Subtotal</div>
                <div class="w-28 py-2 text-center">Ações</div>
            </div>

            @php $total = 0; @endphp
            @foreach ($itens as $item)
                @php
                    $subtotal = $item->quantidade * $item->preco_unitario;
                    $total += $subtotal;
                @endphp
                <div
                    class="flex flex-col sm:flex-row border-t border-gray-300 items-center sm:gap-0 gap-2 py-4 ">
                    {{-- Livro --}}
                    <div class="flex-1 min-w-[180px] sm:px-6 sm:py-3 font-semibold flex items-center">
                        <span class="sm:hidden text-xs font-bold text-gray-400 px-2">Livro</span>
                        {{ $item->livro->titulo }}
                    </div>
                    {{-- Quantidade --}}
                    <div class="sm:w-28 md:px-4 sm:py-3 flex items-center justify-center ">
                        <span class="sm:hidden text-xs font-bold text-gray-400 block mb-1 px-2">Qtd.</span>
                        <form action="{{ route('carrinho.atualizar', ['item' => $item->id]) }}" method="POST" class="flex gap-2 items-center ">
                            @csrf
                            <x-input type="number" name="quantidade" value="{{ $item->quantidade }}" min="1" class="w-14 text-center" />
                            <x-secondary-button type="submit" class="bg-blue-600 text-white hover:bg-blue-700">Atualizar</x-secondary-button>
                        </form>
                    </div>
                    {{-- Preço unitário --}}
                    <div class="sm:w-32 sm:px-4 sm:py-3 flex items-center justify-center text-center">
                        <span class="sm:hidden text-xs font-bold text-gray-400 block mb-1 px-2">Unitário</span>
                        €{{ number_format($item->preco_unitario, 2, ',', '.') }}
                    </div>
                    {{-- Subtotal --}}
                    <div class="sm:w-32 sm:px-4 sm:py-3 flex items-center justify-center text-center ">
                        <span class="sm:hidden text-xs font-bold text-gray-400 block mb-1 px-2">Subtotal</span>
                        €{{ number_format($subtotal, 2, ',', '.') }}
                    </div>
                    {{-- Ações --}}
                    <div class="sm:w-28 sm:px-4 sm:py-3 flex items-center justify-center text-center ">
                        <span class="sm:hidden text-xs font-bold text-gray-400 block mb-1 px-2">Ações</span>
                        <form action="{{ route('carrinho.remover', ['item' => $item->id]) }}" method="POST">
                            @csrf
                            <x-secondary-button type="submit" class="bg-red-600 text-white hover:bg-red-700">Remover</x-secondary-button>
                        </form>
                    </div>
                </div>

            @endforeach

            {{-- Total --}}
            <div class="flex flex-col sm:flex-row bg-gray-100 font-semibold text-gray-700 items-center sm:items-stretch p-4">
                <div class="flex-1 text-right sm:px-4 sm:py-3 text-lg">Total:</div>
                <div class="w-full sm:w-32 sm:px-4 sm:py-3 text-center text-lg">€{{ number_format($total, 2, ',', '.') }}</div>
                <div class="w-full sm:w-28 sm:px-4 sm:py-3"></div>
                <div class="w-full sm:w-28 sm:px-4 sm:py-3"></div>
            </div>
        @endif
        @if (!$itens->isEmpty())
            <div class="mt-6 text-right">
                <x-button as="a" href="{{ route('checkout.index') }}"
                    class="bg-green-600 text-white hover:bg-green-700">
                    Finalizar compra
                </x-button>
            </div>
        @endif
    </div>
</x-app-layout>
