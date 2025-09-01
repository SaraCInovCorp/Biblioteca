<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Autor;
use App\Models\Livro;


class Autor_Livro extends Model
{
    /** @use HasFactory<\Database\Factories\AutorLivroFactory> */
    use HasFactory;

    protected $fillable = [
        'autor_id',
        'livro_id',
    ];

    public function autores(): BelongsTo
    {
        return $this->belongsTo(Autor::class);
    }

    public function livros(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }
}
