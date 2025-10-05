<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pagamento') }}
        </h2>
    </x-slot>
    <div class="flex-1 ">
    <div class="flex-1 max-w-2xl mx-auto p-6">

        <!-- Resumo do Pedido -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <h3 class="font-bold text-lg mb-3">Resumo do pedido</h3>
            <ul>
                @foreach ($encomenda->items as $item)
                    <li>
                        {{ $item->livro->titulo }} (x{{ $item->quantidade }}) - 
                        €{{ number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') }}
                    </li>
                @endforeach
            </ul>

            <p class="font-semibold mt-3">Total: €{{ number_format($encomenda->total, 2, ',', '.') }}</p>
        </div>

        <!-- Endereço de entrega -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <h3 class="font-bold text-lg mb-3">Endereço de entrega</h3>
            <p>
                {{ $encomenda->endereco->tipo }}<br>
                {{ $encomenda->endereco->logradouro }}, {{ $encomenda->endereco->numero }}
                @if ($encomenda->endereco->andereco)
                    , {{ $encomenda->endereco->andereco }}
                @endif
                <br>
                @if ($encomenda->endereco->freguesia)
                    {{ $encomenda->endereco->freguesia }},
                @endif
                {{ $encomenda->endereco->localidade }} -
                {{ $encomenda->endereco->distrito }}
                <br>
                Código Postal: {{ $encomenda->endereco->codigo_postal }}
                <br>
                País: {{ $encomenda->endereco->pais }}
                <br>
                Tel: {{ $encomenda->endereco->telemovel }}
            </p>
        </div>


        <!-- Formulário de pagamento Stripe -->
        <form id="payment-form">
            <div id="payment-element"></div>
            <button id="submit" class="mt-6 px-6 py-3 bg-blue-600 text-white rounded">Pagar</button>
            <div id="payment-message" class="mt-4 text-red-600 hidden"></div>
        </form>

    </div>
</div>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe("{{ env('STRIPE_KEY') }}");
        const elements = stripe.elements({clientSecret: "{{ $clientSecret }}"});

        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');

        document.getElementById('payment-form').addEventListener('submit', async (event) => {
            event.preventDefault();

            const { error: submitError } = await elements.submit();
            if (submitError) {
                document.getElementById('payment-message').classList.remove('hidden');
                document.getElementById('payment-message').textContent = submitError.message;
                return;
            }

            const { error } = await stripe.confirmPayment({
                elements,
                clientSecret: "{{ $clientSecret }}",
                confirmParams: {
                    return_url: "{{ route('checkout.sucesso') }}",
                },
            });

            if (error) {
                console.log(error);
                document.getElementById('payment-message').classList.remove('hidden');
                document.getElementById('payment-message').textContent = error.message;
            }
        });
    </script>

</x-app-layout>
