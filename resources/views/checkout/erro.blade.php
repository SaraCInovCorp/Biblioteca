<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Erro no pagamento') }}
    </h2>
  </x-slot>
  <div class="flex-1 ">
  <div class="max-w-xl mx-auto mt-12 p-8">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-6 rounded">
      <h3 class="text-xl font-bold mb-2">Seu pagamento não foi concluído</h3>
      <p>Houve um problema ao processar o pagamento. Tente novamente ou contate o suporte.</p>
    </div>
    <div class="mt-6 text-center">
      <a href="{{ route('checkout.index') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded">Tentar novamente</a>
    </div>
  </div>
  </div>
</x-app-layout>
