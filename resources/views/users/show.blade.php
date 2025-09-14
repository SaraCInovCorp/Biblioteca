<x-layout>
    <main>
        <h2 class="text-xl font-bold mb-4">
            @if($user)
                Perfil de {{ $user->name }}
            @else
                Histórico de Usuários
            @endif
        </h2>

        @if($isAdmin)
            {{-- Formulário de busca --}}

            <form method="GET" action="{{ route('users.show') }}" class="gap-5 m-6">
                <x-input type="text" name="q" value="{{ $searchTerm }}" placeholder="Buscar usuário por ID ou nome ou e-mail..." class="border rounded px-3 py-2 w-full max-w-md" autocomplete="off" />
                <x-button type="submit">Buscar</x-button>
            </form>
            @if(!empty($message))
                <p class="text-red-600 font-semibold mb-4">{{ $message }}</p>
            @endif
        @endif
        

        @if($user && $isAdmin)
            {{-- Dados do Usuário para Admin --}}
            <div class="flex items-center gap-4 mb-6">
                @if($user->profile_photo_path)
                    <img src="{{ Str::startsWith($user->profile_photo_path, ['http://', 'https://']) ? $user->profile_photo_path : asset('storage/'.$user->profile_photo_path) }}" alt="Foto de perfil de {{ $user->name }}" class="w-16 h-16 rounded-full object-cover">
                @else
                    <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm">Sem foto</div>
                @endif

                <div>
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Perfil:</strong> {{ ucfirst($user->role) }}</p>
                </div>
            </div>
        @endif

        {{-- Histórico (admin somente se fez busca e usuário com histórico) --}}
        @if($historico && $historico->isNotEmpty())
            <h3 class="font-semibold mb-2">Histórico de Requisições</h3>
            @foreach($historico as $req)
                <div class="border rounded p-3 mb-3 bg-white shadow">
                    <p>
                        <strong>
                            <a href="{{ route('requisicoes.show', $req) }}" class="text-blue-600 hover:underline">Requisição {{ $req->id }}</a>
                        </strong> - {{ \Carbon\Carbon::parse($req->data_inicio)->format('d/m/Y') }}
                    </p>
                    <ul class="list-disc list-inside">
                        @foreach($req->items as $item)
                            <li>
                                <a href="{{ route('livros.show', $item->livro) }}" class="text-blue-600 hover:underline">
                                    {{ $item->livro->titulo }}
                                </a> (Status: {{ ucfirst($item->status) }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
            {{ $historico->links() }}
        @elseif($user && (!$isAdmin || ($isAdmin && $searchTerm !== '')))
            <p class="text-gray-600 italic">Nenhuma requisição encontrada para este usuário.</p>
        @endif
    </main>
</x-layout>
