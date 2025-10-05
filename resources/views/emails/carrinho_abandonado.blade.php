<x-mail::message>
# Olá {{ $carrinho->user->name }},

Você adicionou itens ao seu carrinho, mas ainda não finalizou a compra!

<x-mail::table>
| Livro              | Quantidade |
|--------------------|:----------:|
@foreach($carrinho->items as $item)
| {{ $item->livro->titulo }} | {{ $item->quantidade }} |
@endforeach
</x-mail::table>

<x-mail::button :url="route('carrinho.index')">
Finalizar Compra
</x-mail::button>

Os itens continuam reservados para você!

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
