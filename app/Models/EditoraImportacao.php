<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Editora;
use App\Models\Importacao;

class EditoraImportacao extends Model
{
    /** @use HasFactory<\Database\Factories\EditoraImportacaoFactory> */
    use HasFactory;

    protected $table = 'editora_importacao';

    protected $fillable = ['importacao_id', 'editora_id'];

    public function importacao()
    {
        return $this->belongsTo(Importacao::class);
    }

    public function editora()
    {
        return $this->belongsTo(Editora::class);
    }
}
