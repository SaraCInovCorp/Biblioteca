<?php

namespace App\Http\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\Endereco;

class UpdateAddressForm extends Component
{
    public $state = [];

    public $optionsTipos = [
        'entrega' => 'Entrega',
        'pagamento' => 'Pagamento',
    ];

    public function mount()
    {
        $user = Auth::user();

        $endereco = $user->enderecos()->where('tipo', 'entrega')->first() ?? new Endereco();

        $this->state = [
            'logradouro' => $endereco->logradouro ?? '',
            'numero' => $endereco->numero ?? '',
            'andereco' => $endereco->andereco ?? '',
            'freguesia' => $endereco->freguesia ?? '',
            'localidade' => $endereco->localidade ?? '',
            'distrito' => $endereco->distrito ?? '',
            'codigo_postal' => $endereco->codigo_postal ?? '',
            'pais' => $endereco->pais ?? 'Portugal',
            'tipo' => $endereco->tipo ?? 'entrega',
            'telemovel' => $endereco->telemovel ?? '',
        ];
    }

    public function updateAddress()
    {
        $this->validate([
            'state.logradouro' => 'required|string|max:255',
            'state.numero' => 'required|string|max:50',
            'state.andereco' => 'nullable|string|max:255',
            'state.freguesia' => 'nullable|string|max:255',
            'state.localidade' => 'required|string|max:255',
            'state.distrito' => 'required|string|max:255',
            'state.codigo_postal' => 'required|string|max:20',
            'state.pais' => 'required|string|max:255',
            'state.tipo' => 'required|in:entrega,pagamento',
            'state.telemovel' => 'nullable|string|max:25',
        ]);

        $user = Auth::user();

        $endereco = $user->enderecos()->where('tipo', $this->state['tipo'])->first();

        if (! $endereco) {
            $endereco = $user->enderecos()->create($this->state);
        } else {
            $endereco->update($this->state);
        }

        $this->dispatchBrowserEvent('saved');
    }


    public function render()
    {
        return view('profile.update-address-form', [
        'optionsTipos' => $this->optionsTipos,
    ]);
    }
}

