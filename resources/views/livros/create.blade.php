@php
  $editoraSelected = old('editora_id', '');
  $autorSelected = old('autores.0', '');
@endphp
<x-layout>
    <main>
        <div>
            <form method="POST" action="{{ route('livros.store') }}" enctype="multipart/form-data" class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
                @csrf
                <h2 class="text-2xl font-bold mb-6 text-center">Adicionar Novo Livro</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <x-label for="titulo" class="block text-gray-700 font-semibold mb-2">Título:</x-label>
                        <x-input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" required />
                        @error('titulo')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <x-label for="bibliografia" class="block text-gray-700 font-semibold mb-2">Bibliografia:</x-label>
                        <x-textarea name="bibliografia" id="bibliografia" rows="4">{{ old('bibliografia') }}</x-textarea>
                        @error('bibliografia')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="mb-4">
                        <x-label for="preco" class="block text-gray-700 font-semibold mb-2">Preço:</x-label>
                        <x-input type="number" name="preco" id="preco" step="0.01" min="0" value="{{ old('preco') }}" placeholder="Ex: 49.90" required/>
                        @error('preco')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="mb-4">
                        <x-label for="capa" class="block text-gray-700 font-semibold mb-2">Capa (imagem):</x-label>
                        <x-input type="file" name="capa" id="capa" accept="image/*"/>
                        @error('capa')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <x-label for="isbn" class="block text-gray-700 font-semibold mb-2">ISBN:</x-label>
                        <x-input type="number" name="isbn" id="isbn" value="{{ old('isbn') }}" required/>
                        @error('isbn')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <x-label for="editora_id" class="block text-gray-700 font-semibold mb-2">Editora:</x-label>
                        <x-select  
                            name="editora_id" 
                            id="editora_id" 
                            :options="$editoras->pluck('nome', 'id')->toArray()" 
                            :selected="$editoraSelected" 
                            label="editora" 
                        />
                        @error('editora_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror

                        <x-label for="nova_editora" class="block text-gray-500 text-sm mt-1">Nova editora (opcional):</x-label>
                        <x-input type="text" name="nova_editora" id="nova_editora" value="{{ old('nova_editora') }}" placeholder="Nome da nova editora"/>
                        @error('nova_editora')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4 col-span-1 lg:col-span-2" id="autores-wrapper">
                        <x-label for="autores" class="block text-gray-700 font-semibold mb-2">Autor(es):</x-label>

                        <div id="autores-list">
                            <div class="flex items-center gap-2 mb-2 autor-item">
                                <x-select  
                                    name="autores[]" 
                                    id="autores-1" 
                                    :options="$autores->pluck('nome', 'id')->toArray()" 
                                    :selected="$autorSelected" 
                                    label="autor" 
                                />
                                <button type="button" class="remove-autor btn-remove ml-2 text-red-600 font-bold px-2 rounded" title="Remover autor existente">×</button>
                            </div>
                        </div>

                        <div class="mb-4 col-span-1 lg:col-span-2">
                            <x-secondary-button type="button" id="add-autor">
                                Adicionar Outro Autor Existente
                            </x-secondary-button>
                        </div>
                    </div>


                    <div class="mb-4 col-span-1 lg:col-span-2" id="novos-autores-wrapper">
                        <x-label for="novos_autores" class="block text-gray-700 font-semibold mb-2">Novos Autor(es):</x-label>
                        <div id="inputs-novos-autores">
                            <div class="flex items-center gap-2 mb-2 novo-autor-item">
                                <x-input type="text" name="novos_autores[]" id="novo-autor-1" placeholder="Nome de novo autor" class="flex-grow max-w-md" />
                                <button type="button" class="remove-novo-autor btn-remove ml-2 text-red-600 font-bold px-2 rounded" title="Remover novo autor">×</button>
                            </div>
                        </div>
                        <x-secondary-button type="button" id="add-novo-autor">
                            Adicionar Outro Autor Novo
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
        document.getElementById('add-autor').addEventListener('click', function () {
            const wrapper = document.getElementById('autores-list');
            const lastSelectDiv = wrapper.querySelector('div.autor-item');
            const newSelectDiv = lastSelectDiv.cloneNode(true);

            const select = newSelectDiv.querySelector('select');
            if (select) select.value = '';

            const selectsCount = wrapper.querySelectorAll('div.autor-item').length + 1;
            if (select) select.id = 'autores-' + selectsCount;

            wrapper.appendChild(newSelectDiv);
        });

        document.getElementById('autores-list').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-autor')) {
                const items = this.querySelectorAll('div.autor-item');
                if (items.length > 1) { 
                    e.target.closest('div.autor-item').remove();
                }
            }
        });



        document.getElementById('add-novo-autor').addEventListener('click', function () {
            const wrapper = document.getElementById('inputs-novos-autores');
            const lastInputDiv = wrapper.querySelector('div.novo-autor-item');
            const newInputDiv = lastInputDiv.cloneNode(true);

            const input = newInputDiv.querySelector('input');
            if (input) input.value = '';

            wrapper.appendChild(newInputDiv);
        });

        document.getElementById('inputs-novos-autores').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-novo-autor')) {
                const items = this.querySelectorAll('div.novo-autor-item');
                if (items.length > 1) {
                    e.target.closest('div.novo-autor-item').remove();
                }
            }
        });
    </script>
</x-layout>