<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reviews pendentes') }}
        </h2>
    </x-slot>
    <div class="flex-1 ">
        <div>
            <form method="GET" action="{{ route('reviews.index') }}" class="mb-4 flex gap-4 flex-wrap">
                <x-input type="text" name="search_review_id" placeholder="ID Review" value="{{ request('search_review_id') }}"/>
                <x-input type="text" name="search_request_id" placeholder="ID Requisição" value="{{ request('search_request_id') }}"/>
                <x-input type="text" name="search_user" placeholder="Usuário" value="{{ request('search_user') }}" />
                <x-input type="text" name="search_book" placeholder="Livro" value="{{ request('search_book') }}" />
                @php
                    $searchStatusOptions = [
                        '' => 'Todos os status',
                        'ativo' => 'Ativo',
                        'recusado' => 'Recusado',
                        'suspenso' => 'Suspenso',
                    ];
                @endphp

                <x-select 
                    label="status"
                    name="search_status" 
                    :options="$searchStatusOptions" 
                    :selected="request('search_status')" 
                    class="select select-bordered select-sm max-w-xs" 
                />
                <x-button type="submit">Buscar</x-button>
                <x-secondary-button as="a" href="{{ route('reviews.index') }}">Limpar Filtros</x-secondary-button>
            </form>
        </div>
        @if($bookReviews->isEmpty())
            <div class="p-4 text-center text-gray-600 italic">
                Nenhum review encontrado para os filtros fornecidos.
            </div>
        @else
            <form method="POST" action="{{ route('reviews.bulkUpdate') }}">
                @csrf
                @method('POST')  
            <div class="p-4 grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                @if(session('success'))
                    <div class="p-4 mb-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                    @foreach($bookReviews as $bookReview)
                    <div class="bg-white shadow rounded-lg p-4 flex flex-col gap-2 lg:justify-between">
                        <div>
                            <div class="font-semibold text-lg text-blue-800 mb-1">
                                {{ $bookReview->livro->titulo ?? 'Livro desconhecido' }}
                            </div>
                            <div class="text-gray-600 text-sm">
                                <span class="font-semibold">Usuário:</span> {{ $bookReview->user->name ?? 'N/D' }}
                            </div>
                            <div class="text-gray-600 text-sm">
                                <span class="font-semibold">ID review:</span> {{ $bookReview->id }}
                            </div>
                            <div class="text-gray-600 text-sm">
                                <span class="font-semibold">Status:</span> <span class="capitalize">{{ $bookReview->status }}</span>
                            </div>
                            @if($bookReview->review_text)
                            <div class="mt-2 text-gray-800 break-words">
                                <span class="font-semibold">Comentário:</span> {{ $bookReview->review_text }}
                            </div>
                            @endif
                        </div>
                        <div class="flex flex-row gap-2 items-center mt-3 lg:mt-0">
                            <x-secondary-button as="a"  href="{{ route('reviews.show', $bookReview) }}">
                                Ver
                            </x-secondary-button>
                            <x-secondary-button as="a" href="{{ route('reviews.edit', $bookReview) }}">
                                Editar
                            </x-secondary-button>
                            <x-checkbox
                                name="review_ids[]"
                                value="{{ $bookReview->id }}"
                                class="form-checkbox"
                            />
                            <span class="text-gray-600 text-sm font-semibold">Alterar Status</span>
                        </div>
                    </div>
                    @endforeach

                    
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2 space-x-8 m-5 justify-end">
                    @php
                        $statusOptions = [
                            '' => 'Alterar status para...',
                            'ativo' => 'Ativo',
                            'recusado' => 'Recusado',
                            'suspenso' => 'Suspenso',
                        ];
                    @endphp

                    <x-select 
                        label="novostatus"
                        name="new_status" 
                        :options="$statusOptions" 
                        :selected="old('new_status')" 
                        class="select select-bordered select-sm" required
                    />

                    <x-textarea name="admin_justification" placeholder="Justificativa (opcional)"/>
                    <x-secondary-button type="submit">Alterar status</x-secondary-button>
                    </div>
                </form>
                <div>
                    {{ $bookReviews->links() }}
                </div>
            @endif
    </div>
</x-app-layout>