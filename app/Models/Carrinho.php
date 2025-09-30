<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Carrinho extends Model
{
    /** @use HasFactory<\Database\Factories\CarrinhoFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    
    public function items()
    {
        return $this->hasMany(\App\Models\CarrinhoItem::class, 'carrinho_id');
    }
}
