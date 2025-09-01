<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Livro;

class Editora extends Model
{
    /** @use HasFactory<\Database\Factories\EditoraFactory> */
    use HasFactory;

    protected $fillable = [
        'nome',
        'logo_url',
    ];

    public function livros(): HasMany
    {
        return $this->hasMany(Livro::class);
    }

}
