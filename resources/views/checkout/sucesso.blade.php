<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('Pagamento realizado com sucesso!') }}
    </h2>
  </x-slot>
  <div class="flex-1 ">
  <div class="max-w-xl mx-auto mt-12 p-8">
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-6 rounded">
      <h3 class="text-xl font-bold mb-2">Obrigado por sua compra!</h3>
      <p>Seu pedido foi registrado e o pagamento confirmado.</p>
    </div>
    <div class="mt-6 text-center">
      <a href="{{ route('livros.index') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded">Voltar para loja</a>
    </div>
  </div>
  </div>
</x-app-layout>
