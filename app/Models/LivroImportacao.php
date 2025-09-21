<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Livro;
use App\Models\Importacao;

class LivroImportacao extends Model
{
    /** @use HasFactory<\Database\Factories\LivroImportacaoFactory> */
    use HasFactory;

    protected $table = 'livro_importacao';

    protected $fillable = ['importacao_id', 'livro_id'];

    public function importacao()
    {
        return $this->belongsTo(Importacao::class);
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }
}
