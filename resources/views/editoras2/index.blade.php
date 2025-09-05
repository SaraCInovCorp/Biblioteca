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
            <x-label for="nome" class="block font-semibold mb-1">Nome</x-label>
            <x-input type="text" name="nome" id="nome" class="input input-bordered w-full mb-4" required />
            <x-label for="foto" class="block font-semibold mb-1">Foto</x-label>
            <x-input type="file" name="foto" id="foto" class="input input-bordered w-full" />
            <div class="mt-4 flex justify-end gap-2">
                <x-secondary-button type="button" onclick="closeEditCard()" class="btn btn-secondary">Cancelar</x-secondary-button>
                <x-secondary-button type="submit" class="btn btn-primary">Salvar</x-secondary-button>
            </div>
            </form>
        </div>
    </div>
    <div id="edit-card" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center p-4">
            <div class="bg-white p-6 rounded shadow max-w-md w-full relative">
            <x-secondary-button onclick="closeEditCard()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">&times;</x-secondary-button>
            <form id="edit-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="POST" />
                <x-label for="nome" class="block font-semibold mb-1">Nome</x-label>
                <x-input type="text" name="nome" id="nome" class="input input-bordered w-full mb-4" required />
                <x-label for="foto" class="block font-semibold mb-1">Foto</x-label>
                <x-input type="file" name="foto" id="foto" class="input input-bordered w-full" />
                <div class="mt-4 flex justify-end gap-2">
                    <x-secondary-button type="button" onclick="closeEditCard()" class="btn btn-secondary">Cancelar</x-secondary-button>
                    <x-secondary-button type="submit" class="btn btn-primary">Salvar</x-secondary-button>
                </div>
            </form>
        </div>
    </div>
    <main>
        <div class="flex justify-end w-full">
        <div class="mb-6">
            <x-button onclick="openCreateCard()" class="btn btn-primary">
                Nova Editora
            </x-button>
        </div>
        </div>
       <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        

       @foreach ($editoras as $editora)
        <div class="flex items-center gap-4 p-4 rounded border shadow-sm bg-white">
            <img src="{{ Str::startsWith($editora->logo_url, ['http://','https://']) ? $editora->logo_url : asset('storage/'.$editora->logo_url) }}" alt="{{ $editora->nome }}" class="w-16 h-16 rounded object-cover" />
            <div class="flex-grow">
            <p class="font-semibold">{{ $editora->nome }}</p>
            </div>
            <x-secondary-button onclick="openEditCard({{ $editora->id }}, 'editora')" class="btn btn-sm btn-primary">Editar</x-secondary-button>
            <form method="POST" action="{{ route('editoras.destroy', $editora->id) }}" class="inline">
            @csrf
            @method('DELETE')
            <x-secondary-button type="submit" onclick="return confirm('Confirma exclusão?')" class="btn btn-sm btn-danger ml-2">Excluir</x-secondary-button>
            </form>
        </div>
        @endforeach
        </div>
        <div class="mt-6">
            {{ $editoras->links() }}
        </div>
    </main>
    <script>
        function openCreateCard() {
            document.getElementById('edit-form').action = '/editoras';
            document.querySelector('#edit-form input[name="_method"]').value = 'POST';
            document.getElementById('nome').value = '';
            document.getElementById('foto').value = '';
            document.getElementById('edit-card').classList.remove('hidden');
        }

        function openEditCard(id, tipo) {
            fetch(`/${tipo}s/${id}/edit-json`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('edit-form').action = `/${tipo}s/${id}`;
                document.querySelector('#edit-form input[name="_method"]').value = 'PUT';
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