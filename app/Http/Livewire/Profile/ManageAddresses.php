<?php

namespace App\Http\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\Endereco;

class ManageAddresses extends Component
{
    public $addresses = [];
    public $editingAddressId = null;
    public $state = [];
    public $showForm = false;
    public $saved = false;
    public $successMessage;

    public $optionsTipos = [
        'entrega' => 'Entrega',
        'pagamento' => 'Pagamento',
    ];

    public function mount()
    {
        $this->loadAddresses();
    }

    public function loadAddresses()
    {
        $user = Auth::user();
        $this->addresses = $user->enderecos()->get()->toArray();

        if ($this->editingAddressId) {
            $this->loadAddress($this->editingAddressId);
        }
    }

    public function loadAddress($id)
    {
        $address = Endereco::findOrFail($id);
        $this->editingAddressId = $id;
        $this->state = [
            'tipo' => $address->tipo,
            'logradouro' => $address->logradouro,
            'numero' => $address->numero,
            'andereco' => $address->andereco,
            'freguesia' => $address->freguesia,
            'localidade' => $address->localidade,
            'distrito' => $address->distrito,
            'codigo_postal' => $address->codigo_postal,
            'pais' => $address->pais,
            'telemovel' => $address->telemovel,
        ];
    }

     public function editAddress($id)
    {
        $address = Endereco::findOrFail($id);
        $this->editingAddressId = $id;
        $this->state = $address->toArray();
        $this->showForm = true;
    }

    public function createNewAddress()
    {
        $this->showForm = true;
        $this->editingAddressId = null;
        $this->state = [
            'tipo' => 'entrega', 
            'logradouro' => '',
            'numero' => '',
            'andereco' => '',
            'freguesia' => '',
            'localidade' => '',
            'distrito' => '',
            'codigo_postal' => '',
            'pais' => 'Portugal',
            'telemovel' => '',
        ];
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->editingAddressId = null;
        $this->state = [];
    }

    public function saveAddress()
    {
        $validated = $this->validate([
            'state.tipo' => 'required|in:entrega,pagamento',
            'state.logradouro' => 'required|string|max:255',
            'state.numero' => 'required|string|max:50',
            'state.andereco' => 'nullable|string|max:255',
            'state.freguesia' => 'nullable|string|max:255',
            'state.localidade' => 'required|string|max:255',
            'state.distrito' => 'required|string|max:255',
            'state.codigo_postal' => 'required|string|max:20',
            'state.pais' => 'required|string|max:255',
            'state.telemovel' => 'nullable|string|max:25',
        ]);

        $user = Auth::user();

        if ($validated['state']['tipo'] === 'pagamento') {
            $existingPagamento = $user->enderecos()->where('tipo', 'pagamento')->first();

            if ($this->editingAddressId) {
                if ($existingPagamento && $existingPagamento->id != $this->editingAddressId) {
                    $this->addError('state.tipo', 'Já existe um endereço de pagamento cadastrado.');
                    return;
                } else {
                    $address = Endereco::findOrFail($this->editingAddressId);
                    $address->update($validated['state']);
                }
            } else {
                if ($existingPagamento) {
                    $this->addError('state.tipo', 'Já existe um endereço de pagamento cadastrado.');
                    return;
                } else {
                    $address = $user->enderecos()->create($validated['state']);
                    $this->editingAddressId = $address->id;
                }
            }
        } else {
            if ($this->editingAddressId) {
                $address = Endereco::findOrFail($this->editingAddressId);
                $address->update($validated['state']);
            } else {
                $address = $user->enderecos()->create($validated['state']);
                $this->editingAddressId = $address->id;
            }
        }

        $this->loadAddresses();
        $this->saved = true;
        $this->showForm = false;
        $this->dispatch('enderecosAtualizados');
    }

    public function deleteAddress($id)
    {
        $address = auth()->user()->enderecos()->find($id);
        if (!$address) {
            $this->addError('general', 'Endereço não encontrado ou autorizado.');
            return;
        }
        $address->delete();

        $this->dispatch('enderecosAtualizados');
        $this->successMessage = 'Endereço excluído com sucesso.';
        $this->loadAddresses();
    }

    public function render()
    {
        return view('profile.manage-addresses', [
            'addresses' => $this->addresses,
            'optionsTipos' => $this->optionsTipos,
        ]);
    }
}
