<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDocument extends Model
{
    /** @use HasFactory<\Database\Factories\UserDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'data_nascimento',
        'tipo_documento',
        'numero_documento',
        'data_emissao',
        'data_validade',
        'entidade_emissora',
        'nacionalidade',
        'genero',
    ];
    
    protected $casts = [
        'data_nascimento' => 'date',
        'data_emissao' => 'date',
        'data_validade' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
