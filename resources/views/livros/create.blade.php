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
                        <x-select required name="editora_id" id="editora_id" :options="$editoras->pluck('nome', 'id')->toArray()" :selected="old('editora_id')" label="editora" />
                        @error('editora_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4 col-span-1 lg:col-span-2" id="autores-wrapper">
                        <x-label for="autores" class="block text-gray-700 font-semibold mb-2">Autor(es):</x-label>
                        <div class="flex items-center gap-2 mb-2">
                            <x-select  required name="autores[]" id="autores-1" :options="$autores->pluck('nome', 'id')->toArray()" :selected="old('autores', [])" label="autor" />
                        </div>
                    </div>
                    
                    <div class="mb-4 col-span-1 lg:col-span-2">
                        <x-secondary-button type="button" id="add-autor">
                            Adicionar Outro Autor
                        </x-secondary-button>
                    </div>

                    <div class="mb-4 col-span-1 lg:col-span-2">
                        <x-secondary-button as="a" href="{{ route('livros.index') }}" class=" hover:bg-red-700 hover:text-white ">Cancelar</x-secondary-button>
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
            const wrapper = document.getElementById('autores-wrapper');
            const lastSelectDiv = wrapper.querySelector('div.flex');
            const newSelectDiv = lastSelectDiv.cloneNode(true);

            const select = newSelectDiv.querySelector('select');
            select.value = '';

            const selectsCount = wrapper.querySelectorAll('div.flex').length + 1;
            select.id = 'autores-' + selectsCount;

            wrapper.appendChild(newSelectDiv);
        });
    </script>
</x-layout>