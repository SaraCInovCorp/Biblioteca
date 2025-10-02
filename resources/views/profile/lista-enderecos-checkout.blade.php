<div>
    @if($enderecos->isEmpty())
        <p>Nenhum endereço cadastrado.</p>
    @else
        <form>
            <div class="m-4 space-y-2">
                @foreach ($enderecos as $endereco)
                    <x-radio 
                        name="endereco_id" 
                        value="{{ $endereco->id }}" 
                        :id="'endereco_id-'.$endereco->id" 
                        :checked="$loop->first"
                        label="{{ $endereco->logradouro }}, {{ $endereco->numero }} - {{ $endereco->localidade }}"
                        wire:model="selectedEnderecoId"  
                    />
                @endforeach

            </div>
        </form>
    @endif
</div>
