<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Importacao extends Model
{
    /** @use HasFactory<\Database\Factories\ImportacaoFactory> */
    use HasFactory;
    protected $table = 'importacoes';

    protected $fillable = [
        'user_id',
        'api',
        'imported_at',
    ];

    protected $casts = [
        'imported_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function livros(): BelongsToMany
    {
        return $this->belongsToMany(Livro::class, 'livro_importacao')->withTimestamps();
    }

    public function editoras(): BelongsToMany
    {
        return $this->belongsToMany(Editora::class, 'editora_importacao')->withTimestamps();
    }

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_importacao')->withTimestamps();
    }
    
}
