<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class BookRequest extends Model
{
    /** @use HasFactory<\Database\Factories\BookRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'data_inicio',
        'data_fim',
        'notas',
        'ativo',

    ];

    public function items(): HasMany
    {
        return $this->hasMany(BookRequestItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
