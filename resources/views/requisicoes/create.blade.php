<x-layout>
    <main class="max-w-4xl mx-auto p-6" x-data="bookRequestForm()" x-init="init()">

        <h1 class="text-2xl font-bold mb-6">Faça sua requisição</h1>

        <form action="{{ route('requisicoes.store') }}" method="POST">
            @csrf

            {{-- Campo Usuário --}}
            @if(auth()->user()->role === 'admin')
            <div class="mb-4">
                <label class="block font-semibold mb-1" for="userSearch">Usuário</label>
                <input type="text" id="userSearch" x-model="userQuery" @input.debounce.300ms="searchUsers" autocomplete="off"
       placeholder="Buscar usuário" class="border rounded p-2 w-full" />
                <input type="hidden" name="user_id" :value="selectedUserId" />

                <template x-if="userResults.length > 0">
                    <ul class="border rounded max-h-40 overflow-auto bg-white absolute z-10 w-full mt-1">
                        <template x-for="user in userResults" :key="user.id">
                            <li class="p-2 hover:bg-gray-200 cursor-pointer" @click="selectUser(user)" x-text="user.name + ' (' + user.email + ')'"></li>
                        </template>
                    </ul>
                </template>

                <template x-if="selectedUserName">
                    <p class="mt-2 text-green-700 font-semibold">Selecionado: <span x-text="selectedUserName"></span></p>
                </template>
            </div>
        @else
            <div class="mb-4">
                <label class="block font-semibold mb-1">Usuário</label>
                <p class="p-2 bg-gray-100 rounded">{{ auth()->user()->name }}</p>
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}" />
            </div>
        @endif


            {{-- Campo Livro --}}
            <div class="mb-4">
                <label class="block font-semibold mb-1" for="livroSearch">Buscar Livro</label>
                <input type="text" id="livroSearch" x-model="livroQuery" @input.debounce.300ms="searchLivros" autocomplete="off"
                       placeholder="Digite título ou autor" class="border rounded p-2 w-full" />

                <template x-if="livroResults.length > 0">
                    <ul class="border rounded max-h-40 overflow-auto bg-white absolute z-10 w-full mt-1">
                        <template x-for="livro in livroResults" :key="livro.id">
                            <li class="p-2 hover:bg-gray-200 cursor-pointer" @click="addLivro(livro)" x-text="livro.titulo + ' - ' + livro.autor"></li>
                        </template>
                    </ul>
                </template>
            </div>

            {{-- Livros selecionados --}}
            <div id="livroSelecionados" class="mb-6">
                <h3 class="font-bold mb-2">Livros selecionados (até 3)</h3>
                <template x-if="selectedLivros.length === 0">
                    <p class="italic text-gray-500">Nenhum livro selecionado ainda.</p>
                </template>
                <ul>
                    <template x-for="(livro, index) in selectedLivros" :key="livro.id">
                        <li class="flex justify-between items-center mb-1 border rounded p-2">
                            <span x-text="livro.titulo"></span>
                            <button type="button" @click="removeLivro(index)"
                                    class="text-red-600 hover:text-red-900 ml-4 font-bold">X</button>
                            <input type="hidden" :name="'items['+index+'][livro_id]'" :value="livro.id" />
                        </li>
                    </template>
                </ul>
            </div>

            {{-- Datas --}}
            <div class="mb-4">
                <label for="data_inicio" class="block font-semibold mb-1">Data Início</label>
                <input type="date" name="data_inicio" id="data_inicio" x-model="dataInicio" @change="calcularDataFim"
                       required class="border rounded p-2 w-full" />
            </div>

            <div class="mb-6">
                <label for="data_fim" class="block font-semibold mb-1">Data Fim (automático)</label>
                <input type="date" name="data_fim" id="data_fim" :value="dataFim" readonly class="border rounded p-2 w-full bg-gray-100" />
            </div>

            <button type="submit" class="bg-blue-600 text-white rounded px-6 py-2 hover:bg-blue-700" :disabled="selectedLivros.length === 0">
                Enviar Requisição
            </button>

        </form>

    </main>

    <script>
        function bookRequestForm() {
            return {
                isAdmin: @json(auth()->user()->role === 'admin'),
                userQuery: '',
                userResults: [],
                selectedUserId: @json(auth()->user()->role === 'admin' ? null : auth()->user()->id),
                selectedUserName: @json(auth()->user()->role === 'admin' ? null : auth()->user()->name),

                livroQuery: '',
                livroResults: [],
                selectedLivros: [],
                dataInicio: '',
                dataFim: '',

                searchUsers() {
                    if(this.userQuery.length < 2) {
                        this.userResults = [];
                        return;
                    }
                    fetch('/users/search?q=' + encodeURIComponent(this.userQuery), {
                        credentials: 'same-origin'  // envia cookies de sessão para passar auth:sanctum
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log('Usuários recebidos:', data);
                        this.userResults = data;
                    });
                },
                selectUser(user) {
                    this.selectedUserId = user.id;
                    this.selectedUserName = user.name;
                    this.userQuery = user.name;
                    this.userResults = [];
                },

                searchLivros() {
                console.log('Iniciando busca livros para:', this.livroQuery);

                if(this.livroQuery.length < 2) {
                    console.log('Query muito curta, limpando resultados');
                    this.livroResults = [];
                    return;
                }

                fetch('/livros/search?q=' + encodeURIComponent(this.livroQuery), {
                    credentials: 'same-origin'
                })
                .then(res => {
                    console.log('Resposta da fetch status:', res.status);
                    if (!res.ok) {
                        throw new Error('Erro na resposta da API: ' + res.status);
                    }
                    return res.json();
                })
                .then(data => {
                    console.log('Dados recebidos:', data);
                    this.livroResults = data;
                })
                .catch(err => {
                    console.error('Erro ao buscar livros:', err);
                    this.livroResults = [];
                });
            },
                addLivro(livro) {
                    if (this.selectedLivros.length >= 3) {
                        alert('Você pode adicionar no máximo 3 livros.');
                        return;
                    }
                    if (!this.selectedLivros.find(l => l.id === livro.id)) {
                        
                        fetch("/requisicoes/session",
                        {
                            credentials: 'same-origin',
                            headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            method: "POST",
                            body: JSON.stringify({livro})
                        })
                        .then(function(res){ console.log(res); return res.json(); })
                        .then(data => { console.log(data); this.selectedLivros.push(livro); })
                        .catch(function(res){ console.log(res) })
                    }
                    this.livroQuery = '';
                    this.livroResults = [];
                },
                removeLivro(index) {
                    this.selectedLivros.splice(index, 1);
                },

                calcularDataFim() {
                    if (!this.dataInicio) {
                        this.dataFim = '';
                        return;
                    }
                    const data = new Date(this.dataInicio);
                    data.setDate(data.getDate() + 5);
                    this.dataFim = data.toISOString().split('T')[0];
                },

                init() {
                    if (this.dataInicio) {
                        this.calcularDataFim();
                    }
                    setTimeout(() => {
                        fetch("/requisicoes/session",
                        {
                            credentials: 'same-origin',
                            headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            method: "GET"
                        })
                        .then(function(res){ console.log(res); return res.json(); })
                        .then(data => { 
                            console.log(data);
                             data.forEach(item => {
                                if(!this.selectedLivros.some( s => s.id === item.id) ) {
                                    this.selectedLivros.push(item);
                                }
                            });
                        })
                        .catch(function(res){ console.log(res) });
                    }, 2000);
                    

                },

            }
        }
    </script>
</x-layout>
