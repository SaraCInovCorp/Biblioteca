<x-layout>
    <main>
        <div>
            <form method="POST" action="{{ route('livros.update', $livro->id) }}" enctype="multipart/form-data" class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
                @csrf
                @method('PUT')
                <h2 class="text-2xl font-bold mb-6 text-center">Editar {{ $livro->titulo }}</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                        <x-label for="preco" class="block text-gray-700 font-semibold mb-2">Preço:</x-label>
                        <x-input type="number" name="preco" id="preco" step="0.01" min="0" value="{{ old('preco', $livro->preco) }}" placeholder="Ex: 49.90" required/>
                        @error('preco')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="mb-4">
                        <x-label for="capa" class="block text-gray-700 font-semibold mb-2">Capa (imagem):</x-label>
                        <x-input type="file" name="capa" id="capa" accept="image/*"/>
                        @error('capa')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <x-label for="isbn" class="block text-gray-700 font-semibold mb-2">ISBN:</x-label>
                        <x-input type="number" name="isbn" id="isbn" value="{{ old('isbn', $livro->isbn) }}" required/>
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
                                    <input type="text" value="{{ $autor->nome }}" readonly class="input input-bordered flex-grow" />
                                    <button type="button" class="btn btn-sm btn-error" onclick="removerAutor({{ $autor->id }}, this)">Excluir</button>
                                    <input type="hidden" name="autores[]" value="{{ $autor->id }}" />
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
                        <x-secondary-button as="a" href="{{ route('livros.index') }}" class="hover:bg-red-700 hover:text-white ">Cancelar</x-secondary-button>
                        <x-button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                            Salvar Livro
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    </main>
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
    </script>

</x-layout>