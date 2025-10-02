<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Finalizar Compra') }}
        </h2>
    </x-slot>

    <div class="p-6 max-w-7xl mx-auto flex flex-col gap-8">
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-2">Resumo dos livros</h3>
            @if ($itens->isEmpty())
                <p class="text-gray-500">Seu carrinho está vazio.</p>
            @else
                <ul>
                    @foreach ($itens as $item)
                        <li class="flex justify-between border-b py-2">
                            <span>{{ $item->livro->titulo }} (x{{ $item->quantidade }})</span>
                            <span>€{{ number_format($item->quantidade * $item->preco_unitario, 2, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="text-right font-bold text-lg mt-3">
                    Total: €{{ number_format($total, 2, ',', '.') }}
                </div>
            @endif
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-2">Endereço de entrega</h3>
            
            @livewire('profile.manage-addresses')
            
            @if ($enderecos->isNotEmpty())
                <form method="POST" action="{{ route('checkout.processar') }}">
                    @csrf
                    @livewire('profile.lista-enderecos-checkout')

                    <input type="hidden" name="endereco_id" id="inputEnderecoId" />

                    <x-button type="submit">Ir para pagamento</x-button>
                </form>

            @endif

        </div>
    </div>
    <script>
        Livewire.on('enderecoSelecionado', enderecoId => {
            document.getElementById('inputEnderecoId').value = enderecoId;
        });

        document.addEventListener('DOMContentLoaded', () => {
            const checkedRadio = document.querySelector('[name="endereco_id"]:checked');
            if (checkedRadio) {
                document.getElementById('inputEnderecoId').value = checkedRadio.value;
            }
        });
    </script>
</x-app-layout>
