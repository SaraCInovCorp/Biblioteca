<?php

namespace App\Exports;

use App\Models\Livro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class LivrosExport implements FromQuery
{
    use Exportable;

    protected $query;
    protected $editoraId;
    protected $autorId;

    public function __construct($query = null, $editoraId = null, $autorId = null)
    {
        $this->query = $query;
        $this->editoraId = $editoraId;
        $this->autorId = $autorId;
    }

    public function query()
    {
        return Livro::query()
            ->when($this->query, fn($q) => $q->where('titulo', 'like', "%{$this->query}%"))
            ->when($this->editoraId, fn($q) => $q->where('editora_id', $this->editoraId))
            ->when($this->autorId, fn($q) => $q->whereHas('autores', fn($q2) => $q2->where('id', $this->autorId)));
    }
}
