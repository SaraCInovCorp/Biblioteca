<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Editora;
use App\Models\Autor;
use App\Models\Importacao;

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
        'status',
        'origem',
        'user_id',
    ];

    public function editora(): BelongsTo
    {
        return $this->belongsTo(Editora::class);
    }

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_livro')->withTimestamps();
    }

    public function bookRequestItems(): HasMany
    {
        return $this->hasMany(BookRequestItem::class, 'livro_id');
    }

    public function requisicoes()
    {
        return $this->hasManyThrough(BookRequest::class, BookRequestItem::class, 'livro_id', 'id', 'id', 'book_request_id');
    }

    public function importacoes()
    {
        return $this->belongsToMany(Importacao::class, 'livro_importacao')->withTimestamps();
    }

}
