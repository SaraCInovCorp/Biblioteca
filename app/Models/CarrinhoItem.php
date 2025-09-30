<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrinhoItem extends Model
{
    /** @use HasFactory<\Database\Factories\CarrinhoItemFactory> */
    use HasFactory;

    protected $fillable = [
        'carrinho_id', 'livro_id', 'quantidade', 'preco_unitario'
    ];

    public function carrinho()
    {
        return $this->belongsTo(Carrinho::class, 'carrinho_id');
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class, 'livro_id');
    }
}
