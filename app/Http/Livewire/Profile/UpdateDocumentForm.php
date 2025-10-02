<?php

namespace App\Http\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use App\Models\UserDocument;

class UpdateDocumentForm extends Component
{
    public $state = [];
    public $saved = false;

    public function mount()
    {
        $user = Auth::user();
        $doc = $user->document ?? new UserDocument();

        $this->state = [
            'data_nascimento' => $doc->data_nascimento ? Carbon::parse($doc->data_nascimento)->format('Y-m-d') : null,
            'tipo_documento' => $doc->tipo_documento ?? '',
            'numero_documento' => $doc->numero_documento ?? '',
            'data_emissao' => $doc->data_emissao ? Carbon::parse($doc->data_emissao)->format('Y-m-d') : null,
            'data_validade' => $doc->data_validade ? Carbon::parse($doc->data_validade)->format('Y-m-d') : null,
            'entidade_emissora' => $doc->entidade_emissora ?? '',
            'nacionalidade' => $doc->nacionalidade ?? 'Portugal',
            'genero' => $doc->genero ?? '',
        ];
    }

    public function updateDocument()
    {
        $this->validate([
            'state.data_nascimento' => 'nullable|date',
            'state.tipo_documento' => 'nullable|in:BI,CC,Passaporte',
            'state.numero_documento' => 'nullable|string|max:255',
            'state.data_emissao' => 'nullable|date',
            'state.data_validade' => 'nullable|date',
            'state.entidade_emissora' => 'nullable|string|max:255',
            'state.nacionalidade' => 'nullable|string|max:255',
            'state.genero' => 'nullable|in:Masculino,Feminino,Outro',
        ]);

        $user = Auth::user();
        $doc = $user->document ?? new UserDocument(['user_id' => $user->id]);

        $doc->fill($this->state);
        $doc->save();

        $this->saved = true;
    }

    public function render()
    {
        return view('profile.update-document-form');
    }
}

