@php
    use Carbon\Carbon;
    $isAdmin = auth()->user()->role === 'admin';
    $isCidadao = auth()->user()->role === 'cidadao';
    $isOwner = $bookRequest->user_id === auth()->id();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Requisição') }} {{ $bookRequest->id }}
        </h2>
    </x-slot>
            <div class="flex-1 ">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

        <form action="{{ route('requisicoes.update', $bookRequest) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-2 gap-6 p-4">
                    <div>
                        <div class="mb-4">
                            <x-label for="data_inicio" class="block text-gray-700 font-semibold mb-2">Data da Requisição:</x-label>
                            <x-input
                                name="data_inicio"
                                type="date"
                                value="{{ old('data_inicio', $bookRequest->data_inicio ? Carbon::parse($bookRequest->data_inicio)->format('Y-m-d') : '') }}"
                                :readonly="!$isAdmin"
                                class="mb-2"
                            />
                        </div>
                        <div class="mb-4">
                            <x-label for="data_fim" class="block text-gray-700 font-semibold mb-2">Data Prevista da Entrega:</x-label>
                            <x-input
                                name="data_fim"
                                type="date"
                                value="{{ old('data_fim', $bookRequest->data_fim ? Carbon::parse($bookRequest->data_fim)->format('Y-m-d') : '') }}"
                                :readonly="!$isAdmin"
                                class="mb-2"
                            />
                        </div>
                        <div class="mb-4">
                            <x-label for="notas" class="block text-gray-700 font-semibold mb-2">Nota:</x-label>
                            <x-textarea
                                name="notas"
                                type="text"
                                value="{{ old('notas', $bookRequest->notas) }}"
                                :readonly="!$isAdmin"
                                class="mb-2"
                            />
                        </div>
                    </div>

                    {{-- Oculta dados do usuário para o cidadão --}}
                    @if ($isAdmin)
                        <div class="mb-6 p-4 bg-white rounded shadow flex items-center gap-4">
                            @if($bookRequest->user->profile_photo_path)
                                <img src="{{ Str::startsWith($bookRequest->user->profile_photo_path, ['http://', 'https://']) ? $bookRequest->user->profile_photo_path : asset('storage/' . $bookRequest->user->profile_photo_path) }}"
                                    alt="Foto de perfil de {{ $bookRequest->user->name }}"
                                    class="w-16 h-16 rounded-full object-cover">
                            @else
                                <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm">Sem foto</div>
                            @endif

                            <div>
                                <h3 class="font-semibold mb-2">Usuário da Requisição</h3>
                                <p><strong>Nome:</strong> {{ $bookRequest->user->name }}</p>
                                <p><strong>Email:</strong> {{ $bookRequest->user->email }}</p>
                                <p><strong>Perfil:</strong> {{ ucfirst($bookRequest->user->role) }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <h3 class="font-semibold mt-4 mb-2">Itens da Requisição</h3>

                @foreach ($bookRequest->items as $i => $item)
                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                    <div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-2 gap-6 p-4">
                        <div class="mb-4 p-5">
                            <label class="block text-sm font-medium mb-1">Livro</label>

                            @if($isAdmin)
                                <select name="items[{{ $i }}][livro_id]" class="w-full border rounded px-2 py-1">
                                    @foreach ($livros as $livro)
                                        <option value="{{ $livro->id }}" {{ $livro->id == $item->livro_id ? 'selected' : '' }}>
                                            {{ $livro->titulo }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                {{-- cidadão não pode alterar o livro --}}
                                <input type="text" value="{{ $item->livro->titulo }}" disabled class="w-full border rounded px-2 py-1 bg-gray-100" />
                                <input type="hidden" name="items[{{ $i }}][livro_id]" value="{{ $item->livro_id }}">
                            @endif
                        </div>
                        <div class="mb-4 p-5">
                            @if($isAdmin)
                                <x-label for="data_real_entrega" class="block text-gray-700 font-semibold mb-2">Data de Entrega Real:</x-label>
                                <x-input
                                    name="items[{{ $i }}][data_real_entrega]"
                                    type="date"
                                    value="{{ old('items.' . $i . '.data_real_entrega', $item->data_real_entrega ? Carbon::parse($item->data_real_entrega)->format('Y-m-d') : '') }}"
                                    class="mb-2"
                                />
                                <x-label class="block text-gray-700 font-semibold mb-2" for="dias_decorridos_{{ $i }}">Dias Corridos:</x-label>
                                <x-input type="number" id="dias_decorridos_{{ $i }}" value="{{ $item->dias_decorridos ?? '' }}" disabled />
                                <input type="hidden" name="items[{{ $i }}][dias_decorridos]"
                                    id="dias_decorridos_hidden_{{ $i }}" value="{{ $item->dias_decorridos ?? '' }}">
                                <x-label class="block text-sm font-medium mb-1">Status</x-label>
                                <select name="items[{{ $i }}][status]" class="w-full border rounded px-2 py-1">
                                    <option value="realizada" {{ $item->status == 'realizada' ? 'selected' : '' }}>Realizada</option>
                                    <option value="entregue_ok" {{ $item->status == 'entregue_ok' ? 'selected' : '' }}>Entregue OK</option>
                                    <option value="entregue_obs" {{ $item->status == 'entregue_obs' ? 'selected' : '' }}>Entregue com Observação</option>
                                    <option value="nao_entregue" {{ $item->status == 'nao_entregue' ? 'selected' : '' }}>Não entregue</option>
                                    <option value="cancelada" {{ $item->status == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                                <x-label class="block text-gray-700 font-semibold mb-2" for="obs_{{ $i }}">Obs:</x-label>
                                <x-textarea id="obs{{ $i }}" name="items[{{ $i }}][obs]">{{ old("items.$i.obs", $item->obs) }}</x-textarea>
                            @else
                                <p>Data de Entrega Real: {{ old("items.$i.data_real_entrega", $item->data_real_entrega) ? Carbon::parse(old("items.$i.data_real_entrega", $item->data_real_entrega))->format('d/m/Y') : '—' }}</p>
                                <p>Dias Corridos: {{ old("items.$i.dias_decorridos", $item->dias_decorridos) ?? '—' }}</p>
                                <p>Status: {{ ucfirst($item->status) }}</p>
                                <p>Obs: {{ ucfirst($item->obs) }}</p>

                                @if (in_array($item->status, ['entregue_ok', 'entregue_obs']) && $isCidadao && $isOwner)
                                    <div class="mt-3">
                                        <x-label for="items[{{ $i }}][review_text]" class="block text-gray-700 font-semibold mb-2">Seu Review</x-label>
                                        <textarea name="items[{{ $i }}][review_text]" rows="4" class="w-full border rounded px-2 py-1"
                                                placeholder="Deixe seu review sobre o livro aqui...">{{ old("items.$i.review_text", $item->bookReview->review_text ?? '') }}
                                        </textarea>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p>Status: <strong class="capitalize">{{ $item->bookReview->status ?? 'N/D' }}</strong></p>

                                        @if (isset($item->bookReview) && $item->bookReview->status === 'recusado' && $item->bookReview->admin_justification)
                                            <p>Justificativa da recusa:</p>
                                            <div class="p-2 bg-red-100 text-red-800 rounded">
                                                {{ $item->bookReview->admin_justification }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if ($isCidadao && $isOwner)
                                    @if (now()->lt($bookRequest->data_inicio))
                                        <select name="items[{{ $i }}][status]" class="w-full border rounded px-2 py-1">
                                            <option value="{{ $item->status }}" selected>{{ ucfirst($item->status) }}</option>
                                            <option value="cancelada">Cancelar</option>
                                        </select>
                                    @else
                                        <input type="hidden" name="items[{{ $i }}][status]" value="{{ $item->status }}">
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="mt-6 flex justify-between gap-2">
                    <x-secondary-button type="button" onclick="window.history.back()" class="hover:bg-red-700 hover:text-white">Cancelar</x-secondary-button>
                    <x-button type="submit" style="primary">Salvar alterações</x-button>
                </div>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @foreach ($bookRequest->items as $i => $item)
                const dataEntregaInput{{ $i }} = document.querySelector('input[name="items[{{ $i }}][data_real_entrega]"]');
                const diasDisplay{{ $i }} = document.getElementById('dias_decorridos_{{ $i }}');
                const diasHidden{{ $i }} = document.getElementById('dias_decorridos_hidden_{{ $i }}');

                function calcularDiasDecorridos{{ $i }}() {
                    const dataEntregaInput{{ $i }} = document.querySelector('input[name="items[{{ $i }}][data_real_entrega]"]');
                    const diasDisplay{{ $i }} = document.getElementById('dias_decorridos_{{ $i }}');
                    const diasHidden{{ $i }} = document.getElementById('dias_decorridos_hidden_{{ $i }}');
                    const dataInicio = new Date("{{ Carbon::parse($bookRequest->data_inicio)->format('Y-m-d') }}");
                    let value = dataEntregaInput{{ $i }}?.value;
                    if (!value) {
                        if (diasDisplay{{ $i }}) { diasDisplay{{ $i }}.textContent = '-'; }
                        if (diasHidden{{ $i }}) { diasHidden{{ $i }}.value = ''; }
                        return;
                    }
                    const parts = value.split('-');
                    const dataEntrega = new Date(parts[0], parts[1] - 1, parts[2]);

                    if (dataEntrega && dataEntrega >= dataInicio) {
                        const diffTime = dataEntrega - dataInicio;
                        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                        if (diasDisplay{{ $i }}) { diasDisplay{{ $i }}.value = diffDays; }
                        if (diasHidden{{ $i }}) { diasHidden{{ $i }}.value = diffDays; }
                    } else {
                        if (diasDisplay{{ $i }}) { diasDisplay{{ $i }}.textContent = '-'; }
                        if (diasHidden{{ $i }}) { diasHidden{{ $i }}.value = ''; }
                    }
                }

                if (dataEntregaInput{{ $i }}) {
                    dataEntregaInput{{ $i }}.addEventListener('change', calcularDiasDecorridos{{ $i }});
                    calcularDiasDecorridos{{ $i }}();
                }
                @endforeach
            });
        </script>
    </div>
</x-app-layout>