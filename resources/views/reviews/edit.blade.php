<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Review') }}
        </h2>
    </x-slot>
    <div class="max-w-xl mx-auto mt-6 bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('reviews.update', $bookReview) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <span class="font-semibold">Livro:</span> {{ $bookReview->livro->titulo ?? 'Livro desconhecido' }}
            </div>
            <div class="mb-4">
                <span class="font-semibold">Usuário:</span> {{ $bookReview->user->name ?? 'Usuário desconhecido' }}
            </div>
            <div class="mb-4">
                <span class="font-semibold">Comentário:</span>
                <div class="bg-gray-50 rounded p-3 mt-1 text-gray-800">{{ $bookReview->review_text }}</div>
            </div>
            <div class="mb-4">
                <x-label for="status">Status</x-label>
                <select name="status" id="status" class="w-full border rounded px-2 py-1 mt-1">
                    <option value="suspenso" {{ $bookReview->status == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
                    <option value="ativo" {{ $bookReview->status == 'ativo' ? 'selected' : '' }}>Aprovado</option>
                    <option value="recusado" {{ $bookReview->status == 'recusado' ? 'selected' : '' }}>Rejeitado</option>
                </select>
                @error('status')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <x-label for="admin_justification">Justificativa do admin (opcional)</x-label>
                <textarea name="admin_justification" id="admin_justification" rows="3"
                          class="w-full border rounded mt-1">{{ old('admin_justification', $bookReview->admin_justification) }}</textarea>
                @error('admin_justification')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800 transition">Salvar</button>
                <a href="{{ route('reviews.show', $bookReview) }}"
                   class="px-3 py-1 bg-gray-50 text-gray-800 rounded hover:bg-gray-100 text-sm transition">Cancelar</a>
            </div>
        </form>
    </div>
</x-app-layout>