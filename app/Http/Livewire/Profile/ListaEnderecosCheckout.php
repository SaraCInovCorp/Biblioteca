<?php

namespace App\Http\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\Endereco;
use App\Http\Livewire\Profile\ManageAddresses;

class ListaEnderecosCheckout extends Component
{
    public $enderecos;
    public $selectedEnderecoId;

    protected $listeners = ['enderecosAtualizados' => 'atualizarLista'];

    public function mount()
    {
        $this->carregarEnderecos();
    }

    public function carregarEnderecos()
    {
        $this->enderecos = auth()->user()->enderecos()->get();
    }

    public function atualizarLista()
    {
        $this->carregarEnderecos();
    }

    

    public function updatedSelectedEnderecoId($value)
    {
        $this->emitUp('enderecoSelecionado', $value);
    }


    public function render()
    {
        return view('profile.lista-enderecos-checkout');
    }
}
