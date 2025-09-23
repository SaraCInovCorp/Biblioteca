<x-mail::message>
# Nova Review Criada

O usuário **{{ $user->name }}** fez uma nova review para o livro **{{ $livro->titulo }}**.

**Review:**

{{ $review->review_text }}

<x-mail::button :url="$link">
Verificar Review
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
