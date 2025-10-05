<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Encomenda;
use App\Models\CarrinhoItem;
use App\Models\User;

class Carrinho extends Model
{
    /** @use HasFactory<\Database\Factories\CarrinhoFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'status','lembrete_enviado_em','lembrete_enviado_para'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function items()
    {
        return $this->hasMany(CarrinhoItem::class, 'carrinho_id');
    }

    public function encomendas()
    {
        return $this->hasMany(Encomenda::class);
    }

}
