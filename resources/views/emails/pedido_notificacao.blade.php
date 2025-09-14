<x-mail::message>
# Nova Requisição Recebida

Olá Administrador,

Uma nova requisição foi realizada pelo usuário {{ $bookRequest->user->name ?? 'Usuário' }} ({{ $bookRequest->user->email ?? 'email indisponível' }}).

Detalhes da requisição:

<x-mail::table>
| Livro               | Status        |
| ------------------- | ------------- |
@foreach ($bookRequest->items ?? [] as $item)
| {{ $item->livro->titulo ?? 'Livro não encontrado' }} | {{ ucfirst($item->status ?? 'Desconhecido') }} |
@endforeach
</x-mail::table>

@foreach($bookRequest->items ?? [] as $item)
@if(!empty($item->livro) && !empty($item->livro->capa_url))
<img src="{{ $item->livro->capa_url }}" alt="Capa do livro" width="150" style="margin-bottom: 12px;">
<br>
@endif
@endforeach

Atenciosamente,<br>
{{ config('app.name') }}
</x-mail::message>
