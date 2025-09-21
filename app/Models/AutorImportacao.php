<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutorImportacao extends Model
{
    /** @use HasFactory<\Database\Factories\AutorImportacaoFactory> */
    use HasFactory;
    protected $table = 'autor_importacao';

    protected $fillable = [
        'importacao_id',
        'autor_id',
    ];

    public function autor()
    {
        return $this->belongsTo(Autor::class, 'autor_id');
    }

    public function importacao()
    {
        return $this->belongsTo(Importacao::class, 'importacao_id');
    }
    
}
