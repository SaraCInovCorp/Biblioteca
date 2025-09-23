@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes da Review') }}
        </h2>
    </x-slot>
    <div class="max-w-xl mx-auto mt-6 bg-white shadow rounded-lg p-6">
        <div class="mb-4">
            <span class="font-semibold">Livro:</span>
            {{ $bookReview->livro->titulo ?? 'Livro desconhecido' }}
        </div>
        <div class="mb-4">
            <span class="font-semibold">Usuário:</span>
            {{ $bookReview->user->name ?? 'Usuário desconhecido' }}
        </div>
        <div class="mb-4">
            <span class="font-semibold">Status:</span>
            <span class="capitalize">{{ $bookReview->status }}</span>
        </div>
        <div class="mb-4">
            <span class="font-semibold">Comentário:</span>
            <div class="bg-gray-50 rounded p-3 mt-1 text-gray-800">{{ $bookReview->review_text }}</div>
        </div>
        <div class="mb-4">
            <span class="font-semibold">ID da Review:</span>
            {{ $bookReview->id }}
        </div>
        <div class="mb-4">
            <span class="font-semibold">ID do Item da Requisição:</span>
            {{ $bookReview->book_request_item_id }}
        </div>
        <div class="mb-4">
            <span class="font-semibold">Criado em:</span>
            {{ $bookReview->created_at ? $bookReview->created_at->format('d/m/Y H:i') : 'N/D' }}
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reviews.edit', $bookReview->id) }}">Editar</a>
            <a href="{{ route('reviews.index') }}"
               class="px-3 py-1 bg-gray-50 text-gray-800 rounded hover:bg-gray-100 text-sm transition">Voltar</a>
        </div>
    </div>
</x-app-layout>