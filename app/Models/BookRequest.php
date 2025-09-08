<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookRequest extends Model
{
    /** @use HasFactory<\Database\Factories\BookRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'data_inicio',
        'data_fim',
        'notas',
        'ativo',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(BookRequestItem::class);
    }
}
