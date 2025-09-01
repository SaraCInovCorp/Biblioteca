<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Livro;

class Autor extends Model
{
    /** @use HasFactory<\Database\Factories\AutorFactory> */
    use HasFactory;

    protected $table = 'autores';

    protected $fillable = [
        'nome',
        'foto_url',
    ];

    public function livros(): BelongsToMany
    {
        return $this->belongsToMany(Livro::class, 'livros');
    }

}
