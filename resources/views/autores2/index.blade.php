@php
    use Illuminate\Support\Str;
@endphp
<x-layout>
    <div id="edit-card" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center p-4">
        <div class="bg-white p-6 rounded shadow max-w-md w-full relative">
            <x-secondary-button onclick="closeEditCard()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">&times;</x-secondary-button>
            <form id="edit-form" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <!--<input type="hidden" name="_method" value="PUT">-->
                <x-label for="nome" class="block font-semibold mb-1">Nome</x-label>
                <x-input type="text" name="nome" id="nome" class="input input-bordered w-full mb-4" required />
                <x-label for="foto" class="block font-semibold mb-1">Foto</x-label>
                <x-input type="file" name="foto" id="foto" class="input input-bordered w-full" />
                <div class="mt-4 flex justify-end gap-2">
                    
                    <button type="button" onclick="closeEditCard()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>


    <main>
        <div class="flex justify-between w-full">
            <div class="mb-6">
                <h2 class="text-lg font-semibold">Autores</h2>
            </div>
            <div class="mb-6">
                <x-button onclick="openCreateCard()" class="btn btn-primary">
                    Novo Autor
                </x-button>
            </div>
        </div>
       <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        

       @foreach ($autores as $autor)
        <div class="flex items-center gap-4 p-4 rounded border shadow-sm bg-white">
            <img src="{{ Str::startsWith($autor->foto_url, ['http://','https://']) ? $autor->foto_url : asset('storage/'.$autor->foto_url) }}" alt="{{ $autor->nome }}" class="w-16 h-16 rounded object-cover" />
            <div class="flex-grow">
                <p class="font-semibold">{{ $autor->nome }}</p>
            </div>
            <x-secondary-button onclick="openEditCard({{ $autor->id }}, 'autores')" class="btn btn-sm btn-primary">Editar</x-secondary-button>
            <form method="POST" action="{{ route('autores.destroy', $autor->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Confirma exclusão?')" class="btn btn-sm btn-danger ml-2">Excluir</button>
            </form>
        </div>
        @endforeach
        </div>
        <div class="mt-6">
            {{ $autores->links() }}
        </div>
    </main>
    <script>
            function openCreateCard() {
                const form = document.getElementById('edit-form');
                form.action = '/autores';
                form.method = 'POST';
                form.querySelector('input[name="_method"]').value = 'POST'; // Para criar, pode deixar vazio, ou removê-lo
                document.getElementById('nome').value = '';
                document.getElementById('foto').value = '';
                document.getElementById('edit-card').classList.remove('hidden');
            }


            function openEditCard(id, tipo) {
                fetch(`/${tipo}/${id}/edit-json`)
                .then(res => res.json())
                .then(data => {
                    const form = document.getElementById('edit-form');
                    form.action = `/${tipo}/${id}`;
                    form.method = 'POST'; // <-- CORRETO!
                    form.querySelector('input[name="_method"]').value = 'PUT'; // Garante método Laravel
                    document.getElementById('nome').value = data.nome;
                    document.getElementById('foto').value = ''; 
                    document.getElementById('edit-card').classList.remove('hidden');
                });
            }
            function closeEditCard() {
                document.getElementById('edit-card').classList.add('hidden');
    }
        </script>
</x-layout>