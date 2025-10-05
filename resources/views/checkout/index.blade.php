<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Finalizar Compra') }}
        </h2>
    </x-slot>
    <div class="flex-1" x-data="enderecosComponent(@js($enderecos))">
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
                <h3 class="font-bold mb-2">Gerenciar endereços</h3>
                @livewire('profile.manage-addresses')
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Endereço de entrega</h3>

                <form method="POST" action="{{ route('checkout.processar') }}">
                    @csrf
                    <div class="m-4 space-y-2">
                        <template x-for="endereco in enderecos" :key="endereco.id">
                            <label class="flex items-center space-x-2" :for="'endereco_id-' + endereco.id">
                                <input type="radio"
                                       name="endereco_id"
                                       :value="endereco.id"
                                       x-model="selectedEnderecoId"
                                       :id="'endereco_id-' + endereco.id"
                                />
                                <span x-text="`${endereco.logradouro}, ${endereco.numero} - ${endereco.localidade}`"></span>
                            </label>
                        </template>
                    </div>
                    <x-button type="submit">Ir para pagamento</x-button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function enderecosComponent(initialEnderecos) {
            return {
                enderecos: initialEnderecos,
                selectedEnderecoId: initialEnderecos.length ? initialEnderecos[0].id : null,
                init() {
                    window.addEventListener('enderecosAtualizados', event => {
                        console.log(event);
                        this.enderecos = event.detail[0].enderecos;
                        this.selectedEnderecoId = this.enderecos.length ? this.enderecos[0].id : null;
                    });
                }
            };
        }
    </script>
</x-app-layout>
