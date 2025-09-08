<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Editora;
use App\Models\Autor;

class Livro extends Model
{
    /** @use HasFactory<\Database\Factories\LivroFactory> */
    use HasFactory;

    protected $fillable = [
        'titulo',
        'isbn',
        'editora_id',
        'bibliografia',
        'capa_url',
        'preco',
    ];

    public function editora(): BelongsTo
    {
        return $this->belongsTo(Editora::class);
    }

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class);
    }

    public function bookRequestItems(): HasMany
    {
        return $this->hasMany(BookRequestItem::class, 'livro_id');
    }

}
