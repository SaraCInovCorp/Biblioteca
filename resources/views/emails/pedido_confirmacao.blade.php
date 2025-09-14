<x-mail::message>
# Confirmação da sua Requisição

Olá {{ $bookRequest->user->name ?? 'Usuário' }},

Sua requisição foi registrada com sucesso. Seguem os detalhes:

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

Por favor, lembre-se de devolver os livros no prazo.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
