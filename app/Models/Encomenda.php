<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Endereco;
use App\Models\EncomendaItem;
use App\Models\Carrinho;

class Encomenda extends Model
{
    /** @use HasFactory<\Database\Factories\EncomendaFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'endereco_id', 'carrinho_id', 'status', 'total', 'stripe_payment_intent_id', 'payment_status','cancelamento_solicitado','reembolso_aprovado'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'endereco_id');
    }

    public function items()
    {
        return $this->hasMany(EncomendaItem::class, 'encomenda_id');
    }

    public function carrinho()
    {
        return $this->belongsTo(Carrinho::class, 'carrinho_id');
    }

}
