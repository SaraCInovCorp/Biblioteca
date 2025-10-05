<x-app-layout>
    <x-slot name="header">
        <h2>Meus Pedidos</h2>
    </x-slot>
    <div class="flex-1 ">
        <form method="GET" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <x-label value="Data Inicial" for="data_inicio" />
                    <x-input type="date" id="data_inicio" name="data_inicio" value="{{ request('data_inicio') ?? '' }}" class="w-full" />
                </div>
                <div>
                    <x-label value="Data Final" for="data_fim" />
                    <x-input type="date" id="data_fim" name="data_fim" value="{{ request('data_fim') ?? '' }}" class="w-full" />
                </div>
                <div>
                    <x-label value="Livro" for="livro" />
                    <x-input type="text" id="livro" name="livro" placeholder="Título do livro" value="{{ request('livro') ?? '' }}" class="w-full" />
                </div>
                <div>
                    <x-label value="Cancelamento Solicitado" for="cancelamento_solicitado" />
                    <x-select id="cancelamento_solicitado" name="cancelamento_solicitado" label="Cancelamento"
                        :options="['' => 'Todos', '1' => 'Sim', '0' => 'Não']"
                        :selected="request('cancelamento_solicitado')" class="w-full" />
                </div>
                <div>
                    <x-label value="Status do Pedido" for="status" />
                    <x-select
                        id="status"
                        name="status"
                        label="Status do Pedido"
                        :options="['' => 'Todos', 'pendente' => 'Pendente', 'finalizado' => 'Finalizado', 'cancelado' => 'Cancelado']"
                        :selected="request('status')"
                        class="w-full"
                    />
                </div>
                <div class="flex items-end gap-2 h-full">
                    <x-secondary-button type="button" onclick="window.location='{{ route('pedidos.meus') }}'">
                        Limpar
                    </x-secondary-button>
                    <x-button type="submit">Filtrar</x-button>
                </div>
            </div>
        </form>

        <div class="mx-auto grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            @if($pedidos->isEmpty())
                <p>Você ainda não realizou pedidos.</p>
            @else
                @foreach($pedidos as $pedido)
                    <x-card-generic
                        title="Pedido #{{ $pedido->id }}"
                        description="Status: {{ ucfirst($pedido->status) }} | Total: €{{ number_format($pedido->total, 2, ',', '.') }}"
                        :buttonUrl="route('pedidos.meus.detalhe', $pedido)"
                        buttonText="Ver Detalhes"
                        buttonClass="btn btn-wide bg-green-800 text-white hover:bg-green-800/50 hover:text-black transition ease-in-out duration-300 "
                    >
                        <!-- Conteúdo do slot -->
                    

                        <div class="mb-1">Data: {{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                        <ul class="text-sm pt-2">
                            @foreach($pedido->items as $item)
                                <li>{{ $item->livro->titulo }} (x{{ $item->quantidade }})</li>
                            @endforeach
                        </ul>
                    </x-card-generic>
                @endforeach
                <div class="col-span-full">
                    {{ $pedidos->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
