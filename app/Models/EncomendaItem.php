<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Encomenda;
use App\Models\Livro;

class EncomendaItem extends Model
{
    /** @use HasFactory<\Database\Factories\EncomendaItemFactory> */
    use HasFactory;

    protected $fillable = [
        'encomenda_id', 'livro_id', 'quantidade', 'preco_unitario'
    ];

    public function encomenda()
    {
        return $this->belongsTo(Encomenda::class, 'encomenda_id');
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class, 'livro_id');
    }
}
