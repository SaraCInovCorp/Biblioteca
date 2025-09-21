<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Livro;
use App\Models\Importacao;

class Editora extends Model
{
    /** @use HasFactory<\Database\Factories\EditoraFactory> */
    use HasFactory;

    protected $fillable = [
        'nome',
        'logo_url',
        'origem',
        'user_id',
    ];

    public function livros(): HasMany
    {
        return $this->hasMany(Livro::class);
    }

    public function importacoes(): BelongsToMany
    {
        return $this->belongsToMany(Importacao::class, 'editora_importacao')->withTimestamps();
    }

}
