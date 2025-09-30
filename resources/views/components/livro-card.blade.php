@php
    use Illuminate\Support\Str;
@endphp
<div class="card bg-base-100 shadow-sm border border-transparent hover:border-blue-800 group transition-colors duration-300">
    <div class="items-center text-center rounded-t-lg  flex flex-col">
        <div class="card-title  w-full  rounded-t-lg p-6 bg-gray-400 group-hover:text-blue-800 transition-colors duration-300 block">
            <h2 class="w-full text-center"><a href="{{ route('livros.show', ['livro' => $livro->id]) }}">{{ $livro->titulo }}</a></h2>
        </div>

        <div class="card-body py-8 flex flex-row items-center gap-6">
            <div class="flex-shrink-0">
                <img src="{{ Str::startsWith($livro->capa_url, ['http://', 'https://']) ? $livro->capa_url : asset('storage/' . $livro->capa_url) }}" 
     alt="Capa do livro {{ $livro->titulo }}" class="w-28 h-auto rounded-md">
            </div>
            <div class="text-left flex-1">
                <div class="text-sm mt-4 text-justify text-gray-700">
                    {{ Str::limit($livro->bibliografia, 150, '...') }}
                </div>
                <span class="text-sm mb-2 block">
                    <span class="font-semibold">Editora: </span>{{ $livro->editora->nome ?? 'Editora não informada' }}
                </span>
                <p class="text-sm mt-3">
                    <span class="font-semibold">Autor: </span>{{ $livro->autores->pluck('nome')->join(', ') ?? 'Autor não informado' }}
                </p>
                <span class="text-sm font-semibold block mt-2">
                    Status: 
                    @if($livro->status === 'disponivel')
                        <span class="text-green-600 uppercase">Disponível</span>
                    @elseif($livro->status === 'requisitado')
                        <span class="text-yellow-600 uppercase">Requisitado</span>
                    @else
                        <span class="text-red-600 uppercase">{{ ucfirst($livro->status) }}</span>
                    @endif
                </span>
                <p class="text-sm mt-3">
                    <span class="font-semibold">Preço: </span>{{ $livro->preco }}
                </p>
            </div>
        </div>
    </div>
</div>
