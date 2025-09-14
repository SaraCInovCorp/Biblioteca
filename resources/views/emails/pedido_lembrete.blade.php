<x-mail::message>
# Lembrete de Entrega

Olá {{ $bookRequest->user->name ?? 'Usuário' }},

Este é um lembrete de que sua requisição de livros tem data prevista para entrega em **{{ \Carbon\Carbon::parse($bookRequest->data_fim)->timezone('Europe/Lisbon')->format('d/m/Y') }}**.

<x-mail::table>
| Livro                | Status        |
| -------------------- | ------------- |
@foreach($bookRequest->items ?? [] as $item)
| {{ $item->livro->titulo ?? 'Livro não encontrado' }} | {{ ucfirst($item->status ?? 'Desconhecido') }} |
@endforeach
</x-mail::table>
<x-mail::table>
@foreach($bookRequest->items ?? [] as $item)
    @if(!empty($item->livro) && !empty($item->livro->capa_url))
        ![Capa do livro]({{ $item->livro->capa_url }})
        <br>
        <br>
    @endif
@endforeach
</x-mail::table>
    <br>

Por favor, lembre-se de devolver os livros no prazo.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
