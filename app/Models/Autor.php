<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Livro;
use App\Models\Importacao;

class Autor extends Model
{
    /** @use HasFactory<\Database\Factories\AutorFactory> */
    use HasFactory;

    protected $table = 'autores';

    protected $fillable = [
        'nome',
        'foto_url',
        'origem',
        'user_id',
    ];

    public function livros(): BelongsToMany
    {
        return $this->belongsToMany(Livro::class, 'autor_livro')->withTimestamps();
    }

    public function importacoes(): BelongsToMany
    {
        return $this->belongsToMany(Importacao::class, 'autor_importacao')->withTimestamps();
    }

}
