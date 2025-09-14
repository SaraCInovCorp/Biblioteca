<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_real_entrega',
        'dias_decorridos',
        'status',
        'obs',
        'livro_id',
        'book_request_id',
    ];

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class,'livro_id');
    }

    public function bookRequest(): BelongsTo
    {
        return $this->belongsTo(BookRequest::class,'book_request_id');
    }
}
