<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-start w-full">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Importar Livros da API Google Books') }}
            </h2>
        </div>
    </x-slot>
    <div class="flex-1 ">
        @if(session('warning_import'))
        <div class="mb-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
            <strong class="font-bold">Alguns livros não foram importados:</strong>
            <ul class="list-disc list-inside mt-2">
                @foreach(session('warning_import') as $item)
                    <li><strong>{{ $item['titulo'] }}</strong>: {{ $item['motivo'] }}</li>
                @endforeach
            </ul>
            <p class="mt-2">Você pode importar esses livros manualmente pelo formulário de cadastro.</p>
        </div>
        @endif

        @if(session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            {{ session('success') }}
        </div>
        @endif

        <!-- Barra de Pesquisa -->
        <div class="mb-6 flex gap-2 max-w-full">
            <x-input type="text" id="search-query" placeholder="Digite título, autor, editora, ISBN ou tema" 
                   class="flex-grow " />
            <x-button id="btn-search">Buscar</x-button>
        </div>

        <!-- Container de resultados -->
        <form id="import-form" method="POST" action="{{ route('livros.import.store') }}">
            @csrf
            <div id="results-container" class="gap-4 mb-6 hidden"></div>

            <!-- Botão para importar selecionados -->
            <div class="text-center" id="import-actions" style="display:none;">
                <x-button type="submit" class="btn px-8">Importar Livros Selecionados</x-button>
            </div>
        </form>

        <!-- Mensagem de erro genérica -->
        <div id="error-message" class="text-red-600 mt-4 hidden"></div>
</div>

    <script>
        const btnSearch = document.getElementById('btn-search');
        const queryInput = document.getElementById('search-query');
        const resultsContainer = document.getElementById('results-container');
        const importActions = document.getElementById('import-actions');
        const errorMessage = document.getElementById('error-message');
        const importForm = document.getElementById('import-form');

        function clearResults() {
            resultsContainer.innerHTML = '';
            resultsContainer.classList.add('hidden');
            resultsContainer.classList.remove('grid', 'grid-cols-1', 'md:grid-cols-3');
            importActions.style.display = 'none';
            errorMessage.classList.add('hidden');
            errorMessage.textContent = '';
        }

        function createBookDiv(book, index) {
            const volume = book.volumeInfo || {};
            const title = volume.title || 'Sem título';
            const authors = volume.authors ? volume.authors.join(', ') : 'Autor(s) desconhecido(s)';
            const publisher = volume.publisher || 'Editora desconhecida';
            const thumbnail = volume.imageLinks?.thumbnail || 'https://via.placeholder.com/128x195?text=Sem+Imagem';

            const div = document.createElement('div');
            div.className = 'border rounded shadow p-3 flex flex-col items-center gap-2';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'livros[]';
            checkbox.className = 'mb-4';

            checkbox.value = JSON.stringify({
                title: title,
                authors: volume.authors || [],
                publisher: publisher,
                description: volume.description || '',
                isbn: (volume.industryIdentifiers?.find(id => id.type.includes('ISBN'))?.identifier) || '',
                thumbnail: thumbnail,
                industryIdentifiers: volume.industryIdentifiers || []
            });
            checkbox.id = `book-checkbox-${index}`;

            const label = document.createElement('label');
            label.htmlFor = checkbox.id;
            label.className = 'flex flex-col items-center cursor-pointer select-none';
            label.appendChild(checkbox);

            const img = document.createElement('img');
            img.src = thumbnail;
            img.alt = `Capa do livro ${title}`;
            img.className = 'w-32 h-auto object-contain rounded mb-4';

            const titleEl = document.createElement('p');
            titleEl.className = 'font-semibold text-center';
            titleEl.textContent = title;

            const authorsEl = document.createElement('p');
            authorsEl.className = 'text-sm text-gray-600 text-center';
            authorsEl.textContent = authors;

            const publisherEl = document.createElement('p');
            publisherEl.className = 'text-xs text-gray-500 text-center';
            publisherEl.textContent = publisher;

            label.appendChild(img);
            label.appendChild(titleEl);
            label.appendChild(authorsEl);
            label.appendChild(publisherEl);

            div.appendChild(label);

            return div;
        }

        async function searchBooks() {
            clearResults();
            const q = queryInput.value.trim();
            if (!q) {
                errorMessage.textContent = 'Digite um termo para pesquisa.';
                errorMessage.classList.remove('hidden');
                return;
            }
            try {
                const url = new URL("{{ route('livros.import.search') }}");
                url.searchParams.append('q', q);
                const response = await fetch(url.toString());
                const data = await response.json();

                if (data.error) {
                    errorMessage.textContent = data.error;
                    errorMessage.classList.remove('hidden');
                    return;
                }

                if (data.totalItems === 0 || !data.items) {
                    errorMessage.textContent = 'Nenhum livro encontrado para a pesquisa.';
                    errorMessage.classList.remove('hidden');
                    return;
                }

                data.items.forEach((book, index) => {
                    const div = createBookDiv(book, index);
                    resultsContainer.appendChild(div);
                });

                resultsContainer.classList.remove('hidden');
                resultsContainer.classList.add('grid', 'grid-cols-1', 'md:grid-cols-3', 'lg:grid-cols-4');
                importActions.style.display = 'block';
            } catch (err) {
                errorMessage.textContent = 'Erro ao realizar a busca.';
                errorMessage.classList.remove('hidden');
                console.error(err);
            }
        }

        btnSearch.addEventListener('click', searchBooks);

        queryInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchBooks();
            }
        });
    </script>
</x-app-layout>
