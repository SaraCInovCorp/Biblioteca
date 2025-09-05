@php
    use Illuminate\Support\Str;
@endphp
<x-layout>
    <main>
        <div class="flex flex-row items-start justify-center gap-6 mt-6 max-w-5xl mx-auto">
            <div class="flex-shrink-0">
                <img src="{{ Str::startsWith($livro->capa_url, ['http://','https://']) ? $livro->capa_url : asset('storage/'.$livro->capa_url) }}" alt="Capa do livro {{ $livro->titulo }}">
                <x-button type="button" onclick="window.history.back()" class="mt-4 w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                    Voltar
                </x-button>
                <x-button type="button" onclick="window.location='{{ route('livros.edit', $livro->id) }}'" class="mt-4 w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded">
                    Editar
                </x-button>
                <form action="{{ route('livros.destroy', $livro->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar este livro?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="mt-4 w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">
                        Deletar
                    </button>
                </form>
            </div>
            <div class="max-w-3xl p-6 bg-white rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-4">{{ $livro->titulo }}</h2>
                <p class="mt-1"><span class="font-semibold">Bibliografia:</span><span class="text-sm italic block mt-1"> <br/>{{ $livro->bibliografia }}</span></p>
                <p class="mt-4"><span class="font-semibold">Editora:</span> <br/>{{ $livro->editora->nome ?? 'Editora não informada' }}</p>
                <p class="mt-2"><span class="font-semibold">Autor(es):</span> <br/>{{ $livro->autores->pluck('nome')->join(', ') ?? 'Autor não informado' }}</p>
                <p class="mt-2"><span class="font-semibold">ISBN:</span> <br/>{{ $livro->isbn }}</p>
            </div>
        </div>
    </main>
</x-layout>