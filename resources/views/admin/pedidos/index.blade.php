<x-app-layout>
    <x-slot name="header">
        <h2>Todos os Pedidos</h2>
    </x-slot>
    <div class="flex-1 ">
        <div class="mx-auto p-6">
            <form method="GET" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div>
                        <x-label value="Data do Pedido (de)" for="data_pedido_de" />
                        <x-input type="date" id="data_pedido_de" name="data_pedido_de"
                            value="{{ request('data_pedido_de') ?? '' }}" class="w-full" />
                    </div>
                    <div>
                        <x-label value="Data do Pedido (até)" for="data_pedido_ate" />
                        <x-input type="date" id="data_pedido_ate" name="data_pedido_ate"
                            value="{{ request('data_pedido_ate') ?? '' }}" class="w-full" />
                    </div>

                    <div>
                        <x-label value="Livro" for="livro" />
                        <x-input type="text" id="livro" name="livro" placeholder="Título do livro"
                            value="{{ request('livro') ?? '' }}" class="w-full" />
                    </div>
                    <div>
                        <x-label value="Usuário" for="user_id" />
                        <x-select id="user_id" name="user_id" label="Usuário"
                            :options="$users->pluck('name', 'id')->prepend('Todos', '')->toArray()"
                            :selected="request('user_id') ?? ''" class="w-full" />
                    </div>
                    <div>
                        <x-label value="Status do Pedido" for="status" />
                        <x-select id="status" name="status" label="Status do Pedido"
                            :options="['' => 'Todos', 'pendente' => 'Pendente', 'finalizado' => 'Finalizado', 'cancelado' => 'Cancelado']"
                            :selected="request('status')" class="w-full" />
                    </div>
                    <div>
                        <x-label value="Status do Pagamento" for="payment_status" />
                        <x-select id="payment_status" name="payment_status" label="Status do Pagamento"
                            :options="['' => 'Todos', 'pendente' => 'Pendente', 'pago' => 'Pago']"
                            :selected="request('payment_status')" class="w-full" />
                    </div>
                    <div>
                        <x-label value="Cancelamento Solicitado" for="cancelamento_solicitado" />
                        <x-select id="cancelamento_solicitado" name="cancelamento_solicitado" label="Cancelamento"
                            :options="['' => 'Todos', '1' => 'Sim', '0' => 'Não']"
                            :selected="request('cancelamento_solicitado')" class="w-full" />
                    </div>
                    <div class="md:col-span-2 flex items-end gap-2 h-full">
                        <x-secondary-button type="button" onclick="window.location='{{ route('admin.pedidos.index') }}'">
                            Limpar
                        </x-secondary-button>
                        <x-button type="submit">Filtrar</x-button>
                    </div>
                </div>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($pedidos as $pedido)
                    <x-card-generic
                        title="Pedido #{{ $pedido->id }} • {{ $pedido->user->name }}"
                        description="Status: {{ ucfirst($pedido->status) }} | Pagamento: {{ ucfirst($pedido->payment_status) }} | Total: €{{ number_format($pedido->total, 2, ',', '.') }}"
                        :buttonUrl="route('admin.pedidos.show', $pedido)"
                        buttonText="Ver Pedido"
                        class="bg-white"
                        buttonClass="btn btn-wide bg-green-800 text-white hover:bg-green-800/50 hover:text-black transition ease-in-out duration-300 "
                    >
                        <div class="mb-1">Data: {{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                        <ul class="text-sm pt-2">
                            @foreach($pedido->items as $item)
                                <li>{{ $item->livro->titulo }} (x{{ $item->quantidade }})</li>
                            @endforeach
                        </ul>
                    </x-card-generic>
                @empty
                    <p>Nenhum pedido encontrado.</p>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $pedidos->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
