@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhe do ') }} {{ $livro->titulo }}
        </h2>
    </x-slot>
    <div class="flex-1 ">
        <div class="flex flex-row items-start justify-center gap-6 mt-6 max-w-5xl mx-auto">
            <div class="flex-shrink-0">
                <img src="{{ Str::startsWith($livro->capa_url, ['http://','https://']) ? $livro->capa_url : asset('storage/'.$livro->capa_url) }}" 
                    alt="Capa do livro {{ $livro->titulo }}" 
                    style="max-width: 300px; height: auto;">
                <x-button type="button" onclick="window.history.back()" class="mt-4 w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                    Voltar
                </x-button>
                @if(auth()->check() && $livro->status === 'disponivel')
                    <x-button
                        type="button"
                        class="mt-4 w-full bg-green-900 hover:bg-green-400 text-white font-semibold py-2 px-4 rounded"
                        onclick="adicionarLivroEIrCriar({{ $livro->id }}, '{{ addslashes($livro->titulo) }}', '{{ addslashes($livro->autor ?? '') }}')"
                    >
                        Requisitar este livro
                    </x-button>
                @endif
                @if(auth()->check() && !auth()->user()->isAdmin() && $livro->status === 'indisponivel' && !$estaInscrito)
                    <x-button
                        type="button"
                        class="mt-4 w-full bg-blue-700 hover:bg-blue-400 text-white font-semibold py-2 px-4 rounded"
                        onclick="inscreverListaEspera({{ $livro->id }})"
                    >
                        Avise-me quando disponível
                    </x-button>
                @elseif(auth()->check() && !auth()->user()->isAdmin() && $estaInscrito)
                    <p class="mt-4 text-gray-600 font-medium text-center align-middle" style="width: 14rem;">Você já está inscrito para ser avisado quando este livro ficar disponível.</p>
                @endif
                @if(auth()->check() && auth()->user()->isAdmin())
                <x-button type="button" onclick="window.location='{{ route('livros.edit', $livro->id) }}'" class="mt-4 w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded">
                    Editar
                </x-button>
                <form action="{{ route('livros.destroy', $livro->id) }}" method="POST" onsubmit="return confirm('Confirma alteração do status do livro?');">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" class="mt-4 w-full bg-red-600 hover:bg-red-300 text-white font-semibold py-2 px-4 rounded">
                        Alternar Disponibilidade
                    </x-button>
                </form>

                @endif
                @foreach ($approvedReviews as $review)
                    <x-card-review-p 
                        userName="{{ $review->user->name }}" 
                        reviewText="{{ $review->review_text }}" 
                        status="{{ $review->status }}"
                        style="width: 14rem;" 
                    />

                @endforeach
            </div>
            <div class="max-w-3xl p-6 bg-white rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-4">{{ $livro->titulo }}</h2>
                <p class="mt-1"><span class="font-semibold">Bibliografia:</span><span class="text-sm italic block mt-1"> <br/>{{ $livro->bibliografia }}</span></p>
                <p class="mt-4"><span class="font-semibold">Editora:</span> <br/>{{ $livro->editora->nome ?? 'Editora não informada' }}</p>
                <p class="mt-2"><span class="font-semibold">Autor(es):</span> <br/>{{ $livro->autores->pluck('nome')->join(', ') ?? 'Autor não informado' }}</p>
                <p class="mt-2"><span class="font-semibold">Preço:</span>  <br/>€ {{  $livro->preco }}</p>
                <p class="mt-2">
                    <span class="font-semibold">Status:</span>   <br/>
                    @if($livro->status === 'disponivel')
                        <span class="text-green-600 uppercase">Disponível</span>
                    @elseif($livro->status === 'requisitado')
                        <span class="text-yellow-600 uppercase">Requisitado</span>
                    @else
                        <span class="text-red-600 uppercase">{{ ucfirst($livro->status) }}</span>
                    @endif
                </p>

                <p class="mt-2"><span class="font-semibold">ISBN:</span> <br/>{{ $livro->isbn }}</p>
                <x-stats title="Requisições" :total="$totalRequisicoes" :tusuarios="$totalUsuarios"  />
                    @if(auth()->check() && !$historico->isEmpty())
                        <ul class="space-y-3">
                            @foreach($historico as $item)
                                <li class="bg-gray-50 p-3 rounded shadow">
                                    <p>
                                        @if(auth()->user()->isAdmin())
                                            <strong>
                                                <a href="{{ route('requisicoes.show', $item->bookRequest) }}" class="text-blue-600 hover:underline">
                                                    Requisição #{{ $item->bookRequest->id }}
                                                </a>
                                            </strong> - {{ $item->bookRequest->user->name ?? 'Usuário deletado' }}
                                        @else
                                            <a href="{{ route('requisicoes.show', $item->bookRequest) }}" class="text-blue-600 hover:underline">
                                                Detalhes da sua requisição #{{ $item->bookRequest->id }}
                                            </a>
                                        @endif
                                    </p>
                                    <p><strong>Data Início:</strong> {{ Carbon::parse($item->bookRequest->data_inicio)->format('d/m/Y') }}</p>
                                    <p><strong>Status do item:</strong> {{ ucfirst($item->status) }}</p>
                                    <p>
                                        
                                        @if ($item->data_real_entrega)
                                            <strong>Data de Entrega:</strong>
                                                {{ Carbon::parse($item->data_real_entrega)->format('d/m/Y') }}
                                        @else
                                            <strong>Data de Previsão de Entrega:</strong>
                                                {{ $item->bookRequest->data_fim ? Carbon::parse($item->bookRequest->data_fim)->format('d/m/Y') : 'Não entregue' }}
                                        @endif
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    @elseif(auth()->check() && $historico->isEmpty())
                        <p class="text-gray-600 italic">
                            @if(auth()->user()->isAdmin())
                                Nenhuma requisição para este livro.
                            @else
                                Você não realizou nenhuma requisição deste livro.
                            @endif
                        </p>
                    @endif

                    @if($livrosRelacionados->isNotEmpty())
                        <section id="livros-relacionados">
                            @foreach($livrosRelacionados as $relacionado)
                                <x-card-generic
                                    :imageUrl="$relacionado->capa_url"
                                    :title="$relacionado->titulo"
                                    :description="Str::limit($relacionado->bibliografia, 120, '...')"
                                    buttonText="Ver Detalhes"
                                    :buttonUrl="route('livros.show', $relacionado->id)"
                                    class="border rounded p-4 hover:shadow-lg transition mb-4"
                                >
                                    <p class="text-xs text-gray-500 mt-2">Similaridade: {{ number_format($relacionado->similaridade, 2) }}%</p>
                                </x-card-generic>
                            @endforeach
                        </section>

                        @if($livrosRelacionados->count() >= 2)
                            <div class="text-center mt-6">
                                <x-secondary-button id="btn-carregar-mais">Buscar mais relacionados</x-secondary-button>
                            </div>
                        @endif
                    @endif
            </div>
        </div>
        <script>
            let paginaAtual = 2;
            const livroId = "{{ $livro->id }}";
            const btnCarregarMais = document.getElementById('btn-carregar-mais');
            const containerRelacionados = document.getElementById('livros-relacionados');

            btnCarregarMais.addEventListener('click', async () => {
                btnCarregarMais.disabled = true;
                btnCarregarMais.innerText = 'Carregando...';

                try {
                    const resposta = await fetch(`/livros/${livroId}/relacionados?page=${paginaAtual}`);
                    const dados = await resposta.json();

                    dados.livros.forEach(livro => {
                        const card = document.createElement('div');
                        card.classList.add('card', 'card-side', 'bg-base-100', 'shadow-sm', 'border', 'rounded', 'p-4', 'hover:shadow-lg', 'transition', 'mb-4');

                        const imgUrl = livro.capa_url 
                            ? (livro.capa_url.startsWith('http') ? livro.capa_url : `/storage/${livro.capa_url}`)
                            : '';

                        card.innerHTML = `
                            <figure class="w-48 ${imgUrl ? '' : 'flex items-center justify-center bg-gray-100 rounded'}" style="min-height: 10rem;">
                                ${imgUrl ? `<img src="${imgUrl}" alt="${livro.titulo}" style="object-fit:contain;max-height:160px;" />` : '<div class="text-gray-400 italic text-xs p-4">Sem capa</div>'}
                            </figure>
                            <div class="card-body">
                                <h2 class="card-title">${livro.titulo}</h2>
                                <p>${livro.bibliografia ? (livro.bibliografia.length > 120 ? livro.bibliografia.substring(0,120) + '...' : livro.bibliografia) : ''}</p>
                                <p class="text-xs text-gray-500 mt-2">Similaridade: ${livro.similaridade.toFixed(2)}%</p>
                                <div class="card-actions justify-end">
                                    <a href="/livros/${livro.id}" class="btn btn-wide bg-gray-800 text-white hover:bg-gray-500 transition">
                                        Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        `;
                        containerRelacionados.appendChild(card);
                    });

                    paginaAtual++;

                    if (!dados.hasMore) {
                        btnCarregarMais.style.display = 'none';
                    } else {
                        btnCarregarMais.disabled = false;
                        btnCarregarMais.innerText = 'Buscar mais relacionados';
                    }
                } catch (error) {
                    console.error(error);
                    btnCarregarMais.disabled = false;
                    btnCarregarMais.innerText = 'Buscar mais relacionados';
                    alert('Erro ao carregar mais livros relacionados.');
                }
            });
            function adicionarLivroEIrCriar(id, titulo, autor) {
                console.log({id, titulo, autor});

                fetch("{{ route('requisicoes.session.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        livro: { id: id, titulo: titulo, autor: autor }
                    }),
                })
                .then(async response => {
                    if (!response.ok) {
                        const data = await response.json().catch(() => ({}));
                        throw new Error(data.error || 'Erro ao adicionar livro');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Livro adicionado:', data);
                    window.location.href = "{{ route('requisicoes.create') }}";
                })
                .catch(error => {
                    alert(`Erro: ${error.message}`);
                    console.error('Falha ao adicionar livro:', error);
                });
            }


            function inscreverListaEspera(livroId) {
                fetch("{{ route('livro-waiting-list.store', ['livro' => '__id__']) }}".replace('__id__', livroId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({}),
                    credentials: 'same-origin',
                })
                .then(async response => {
                    if (!response.ok) {
                        const data = await response.json().catch(() => ({}));
                        throw new Error(data.error || 'Erro ao inscrever');
                    }
                    return response.json();
                })
                .then(data => {
                    alert('Inscrição realizada com sucesso! Você será notificado quando o livro estiver disponível.');
                    location.reload();
                })
                .catch(error => {
                    alert(`Erro: ${error.message}`);
                    console.error('Falha ao inscrever na lista de espera:', error);
                });
            }

        </script>
    </div>
</x-app-layout>