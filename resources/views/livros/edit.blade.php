<script>
    const availableAutores = @json($autores);
</script>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar ') }} {{ $livro->titulo }}
        </h2>
    </x-slot>
    <div class="flex-1 ">
            <form method="POST" action="{{ route('livros.update', $livro->id) }}" enctype="multipart/form-data" class="mx-auto p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="mb-8">
                        <x-button type="button" id="btn-google-edit">Buscar dados na API Google Books</x-button>
                    </div>

                    <div id="api-result-edit" class="mb-6 hidden"></div>

                    <div class="mb-4">
                        <x-label for="titulo" class="block text-gray-700 font-semibold mb-2">Título:</x-label>
                        <x-input type="text" name="titulo" id="titulo" value="{{ old('titulo', $livro->titulo) }}" required />
                        @error('titulo')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <x-label for="bibliografia" class="block text-gray-700 font-semibold mb-2">Bibliografia:</x-label>
                        <x-textarea name="bibliografia" id="bibliografia" rows="4">{{ old('bibliografia', $livro->bibliografia) }}</x-textarea>
                        @error('bibliografia')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <x-label for="status" class="block text-gray-700 font-semibold mb-2">Status:</x-label>
                        <select name="status" id="status" required class="input input-bordered w-full">
                            <option value="disponivel" {{ old('status', $livro->status) === 'disponivel' ? 'selected' : '' }}>Disponível</option>
                            <option value="indisponivel" {{ old('status', $livro->status) === 'indisponivel' ? 'selected' : '' }}>Indisponível</option>
                            <option value="requisitado" {{ old('status', $livro->status) === 'requisitado' ? 'selected' : '' }}>Requisitado</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <x-label for="preco" class="block text-gray-700 font-semibold mb-2">Preço:</x-label>
                        <x-input type="number" name="preco" id="preco" step="0.01" min="0" value="{{ old('preco', $livro->preco) }}" placeholder="Ex: 49.90" required/>
                        @error('preco')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <x-label for="capa" class="block text-gray-700 font-semibold mb-2">Capa (imagem):</x-label>
                        <x-input type="file" name="capa" id="capa" accept="image/*"/>
                        <input type="hidden" id="capa_url" name="capa_url" value="{{ old('capa_url', $livro->capa_url) }}" />
                        @error('capa')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <x-label for="isbn" class="block text-gray-700 font-semibold mb-2">ISBN:</x-label>
                        <x-input type="text" name="isbn" id="isbn" value="{{ old('isbn', $livro->isbn) }}" required/>
                        @error('isbn')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <x-label for="editora_id" class="block text-gray-700 font-semibold mb-2">Editora:</x-label>
                        <x-select required name="editora_id" id="editora_id" :options="$editoras->pluck('nome', 'id')->toArray()" :selected="old('editora_id', $livro->editora_id)" label="editora" />
                        @error('editora_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4 col-span-1 lg:col-span-2">
                        <x-label class="block text-gray-700 font-semibold mb-2">Autores atuais:</x-label>
                        <ul id="lista-autores" class="mb-2">
                            @foreach ($livro->autores as $autor)
                                <li class="flex items-center gap-2 mb-1">
                                    <x-input type="text" value="{{ $autor->nome }}" readonly class="input input-bordered flex-grow" />
                                    <button type="button" class="btn btn-sm btn-error" onclick="removerAutor({{ $autor->id }}, this)">Excluir</button>
                                    <x-input type="hidden" name="autores[]" value="{{ $autor->id }}" />
                                </li>
                            @endforeach
                        </ul>

                        <x-label class="block text-gray-700 font-semibold mb-2">Adicionar novo autor:</x-label>
                        <div id="autores-wrapper" class="flex items-center gap-2 mb-2">
                            <x-select required name="autores[]" id="autores-1" :options="$autores->pluck('nome', 'id')->toArray()" :selected="old('autores.0', $livro->autores->first()->id ?? '')" label="autor" />
                        </div>
                    </div>

                    <div class="mb-4 col-span-1 lg:col-span-2">
                        <x-secondary-button type="button" id="add-autor">
                            Adicionar Outro Autor
                        </x-secondary-button>
                    </div>

                    <div class="mb-4 col-span-1 lg:col-span-2">
                        <x-secondary-button as="a" href="{{ route('livros.index') }}" class="hover:bg-red-700 hover:text-white">Cancelar</x-secondary-button>
                        <x-button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                            Salvar Livro
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    

    <script>
        function removerAutor(autorId, btn) {
            btn.closest('li').remove();
        }

        document.getElementById('add-autor').addEventListener('click', function () {
            const wrapper = document.getElementById('autores-wrapper');
            const lastSelect = wrapper.querySelector('select');
            const newSelect = lastSelect.cloneNode(true);
            newSelect.value = '';
            const selectsCount = wrapper.querySelectorAll('select').length + 1;
            newSelect.id = 'autores-' + selectsCount;
            wrapper.appendChild(newSelect);
        });

        function preencheCampo(id, valor) {
            document.getElementById(id).value = valor || '';
        }

        async function atualizaEditoraPorNome(nomeEditora) {
            if (!nomeEditora) return;
            const url = new URL("{{ route('editoras.check') }}");
            url.searchParams.append('nome', nomeEditora);
            const res = await fetch(url.toString());
            const data = await res.json();
            if (data && data.id) {
                document.getElementById('editora_id').value = data.id;
            }
        }

        async function atualizarAutoresPorNomes(nomes) {
            if (!nomes || nomes.length === 0) return;

            const url = new URL("{{ route('autores.check') }}");
            nomes.forEach(nome => url.searchParams.append('nomes[]', nome));

            const res = await fetch(url.toString());
            const autores = await res.json();

            const wrapper = document.getElementById('autores-wrapper');
            wrapper.innerHTML = '';

            autores.forEach((autor, idx) => {
                const select = document.createElement('select');
                select.name = 'autores[]';
                select.id = 'autores-' + (idx + 1);
                select.required = true;
                select.className = 'input input-bordered';

                @foreach ($autores as $a)
                    let option{{ $a->id }} = document.createElement('option');
                    option{{ $a->id }}.value = '{{ $a->id }}';
                    option{{ $a->id }}.text = '{{ $a->nome }}';
                    select.appendChild(option{{ $a->id }});
                @endforeach

                if (autor.id) {
                    select.value = autor.id;
                }
                wrapper.appendChild(select);
            });
        }
</script>
<script>
    console.log('Listener carregado');
        document.getElementById('btn-google-edit').addEventListener('click', async () => {
            console.log('Listener carregado');
            const isbn = document.getElementById('isbn').value.trim();
            if (!isbn) {
                alert('Digite o ISBN para buscar.');
                return;
            }
            const url = "{{ route('google-books.search') }}?q=isbn:" + encodeURIComponent(isbn);
            const res = await fetch(url);
            const data = await res.json();
            if (data.totalItems > 0) {
                const livro = data.items[0].volumeInfo;
                const livropreco = data.items[0].saleInfo;

                const resultadoDiv = document.getElementById('api-result-edit');
                resultadoDiv.innerHTML = '';

                const container = document.createElement('div');
                container.className = 'bg-gray-100 p-4 rounded';

                const h3 = document.createElement('h3');
                h3.className = 'text-lg font-bold mb-1';
                h3.textContent = `Título API: ${livro.title}`;
                container.appendChild(h3);

                const btnTitulo = document.createElement('button');
                btnTitulo.type = 'button';
                btnTitulo.textContent = 'Usar este título';
                btnTitulo.className = 'btn px-2 py-2 rounded shadow bg-gray-200 hover:text-white hover:bg-gray-800 transition duration-300';
                btnTitulo.addEventListener('click', () => {
                    preencheCampo('titulo', livro.title || '');
                });
                container.appendChild(btnTitulo);

                const divBib = document.createElement('div');
                divBib.className = 'mt-4';
                divBib.textContent = `Bibliografia: ${livro.description || ''}`;
                container.appendChild(divBib);

                const btnBib = document.createElement('button');
                btnBib.type = 'button';
                btnBib.textContent = 'Usar esta bibliografia';
                btnBib.className = 'btn px-2 py-2 rounded shadow bg-gray-200 hover:text-white hover:bg-gray-800 transition duration-300';
                btnBib.addEventListener('click', () => {
                    preencheCampo('bibliografia', livro.description || '');
                });
                container.appendChild(btnBib);

                const divEditora = document.createElement('div');
                divEditora.className = 'mt-4';
                divEditora.textContent = `Editora: ${livro.publisher || ''}`;
                container.appendChild(divEditora);

                const btnEditora = document.createElement('button');
                btnEditora.type = 'button';
                btnEditora.textContent = 'Usar esta editora';
                btnEditora.className = 'btn px-2 py-2 rounded shadow bg-gray-200 hover:text-white hover:bg-gray-800 transition duration-300';
                btnEditora.addEventListener('click', () => {
                    atualizaEditoraPorNome(livro.publisher || '');
                });
                container.appendChild(btnEditora);

                const divAutores = document.createElement('div');
                divAutores.className = 'mt-4';
                divAutores.textContent = `Autores: ${(livro.authors || []).join(', ')}`;
                container.appendChild(divAutores);

                const btnAutores = document.createElement('button');
                btnAutores.type = 'button';
                btnAutores.textContent = 'Usar autores';
                btnAutores.className = 'btn px-2 py-2 rounded shadow bg-gray-200 hover:text-white hover:bg-gray-800 transition duration-300';
                btnAutores.addEventListener('click', () => {
                    atualizarAutoresPorNomes(livro.authors || []);
                });
                container.appendChild(btnAutores);

                const divCapa = document.createElement('div');
                divCapa.className = 'mt-4';
                const imgCapa = document.createElement('img');
                imgCapa.src = livro.imageLinks?.thumbnail || '';
                imgCapa.alt = 'Capa do livro';
                divCapa.appendChild(imgCapa);
                container.appendChild(divCapa);

                const btnCapa = document.createElement('button');
                btnCapa.type = 'button';
                btnCapa.textContent = 'Usar esta foto';
                btnCapa.className = 'btn px-2 py-2 rounded shadow bg-gray-200 hover:text-white hover:bg-gray-800 transition duration-300';
                btnCapa.addEventListener('click', () => {
                    preencheCampo('capa_url', livro.imageLinks?.thumbnail || '');
                });
                container.appendChild(btnCapa);

                const precoValor = livropreco?.listPrice?.amount || '';
                const precoMoeda = livropreco?.listPrice?.currencyCode || '';

                const h3p = document.createElement('h3');
                h3p.className = 'text-lg font-bold mb-1';
                h3p.textContent = precoValor ? `Preço: ${precoValor} ${precoMoeda}` : 'Preço não disponível';
                container.appendChild(h3p);

                const btnPreco = document.createElement('button');
                btnPreco.type = 'button';
                btnPreco.textContent = 'Usar este preço';
                btnPreco.className = 'btn px-2 py-2 rounded shadow bg-gray-200 hover:text-white hover:bg-gray-800 transition duration-300';
                btnPreco.addEventListener('click', () => {
                    preencheCampo('preco', precoValor);
                });
                container.appendChild(btnPreco);

                resultadoDiv.appendChild(container);
                resultadoDiv.classList.remove('hidden');
            } else {
                alert('Nenhum livro encontrado para este ISBN.');
            }
        });
    </script>
    </div>
</x-app-layout>