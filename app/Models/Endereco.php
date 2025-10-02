<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Encomenda;

class Endereco extends Model
{
    /** @use HasFactory<\Database\Factories\EnderecoFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'tipo', 'logradouro', 'numero', 'andereco', 'freguesia', 'localidade',
        'distrito', 'codigo_postal', 'pais', 'telemovel'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function encomendas()
    {
        return $this->hasMany(Encomenda::class, 'endereco_id');
    }
}
