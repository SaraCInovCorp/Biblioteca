<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pagamento') }}
        </h2>
    </x-slot>
    <div class="flex-1 ">
        <div class="max-w-2xl mx-auto p-6">
            <h1 class="text-2xl font-bold mb-6">Pagamento</h1>

            <div id="payment-element"><!-- Stripe Elements monta aqui --></div>
            <button id="submit" class="mt-6 px-6 py-3 bg-blue-600 text-white rounded">Pagar</button>

            <div id="payment-message" class="mt-4 text-red-600 hidden"></div>
        </div>
    </div>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            const stripe = Stripe("{{ env('STRIPE_KEY') }}");
            const elements = stripe.elements();

            const clientSecret = "{{ request('clientSecret') }}";
            const paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');

            const submitButton = document.getElementById('submit');
            const paymentMessage = document.getElementById('payment-message');

            submitButton.addEventListener('click', async () => {
                submitButton.disabled = true;

                const {error} = await stripe.confirmPayment({
                    elements,
                    clientSecret,
                    confirmParams: {
                        return_url: "{{ route('checkout.sucesso') }}",
                    },
                });

                if (error) {
                    paymentMessage.textContent = error.message;
                    paymentMessage.classList.remove('hidden');
                    submitButton.disabled = false;
                }
            });
        </script>
</x-app-layout>
