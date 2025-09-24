<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Livro;

class LivroWaitingList extends Model
{
    /** @use HasFactory<\Database\Factories\LivroWaitingListFactory> */
    use HasFactory;

    protected $fillable = [
        'livro_id',
        'user_id',
        'ativo',
        'notificado_em',
    ];

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
