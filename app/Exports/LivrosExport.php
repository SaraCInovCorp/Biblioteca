<?php

namespace App\Exports;

use App\Models\Livro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LivrosExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $query;
    protected $editoraId;
    protected $autorId;
    protected $ids;

    public function __construct($query = null, $editoraId = null, $autorId = null, $ids = [])
    {
        $this->query = $query;
        $this->editoraId = $editoraId;
        $this->autorId = $autorId;
        if (is_string($ids)) {
            $decoded = json_decode($ids, true);
            if (is_array($decoded)) {
                $this->ids = $decoded;
            } else {
                $this->ids = explode(',', $ids);
            }
        } else {
            $this->ids = (array) $ids;
        }
    }

    public function query()
    {
        $query = Livro::query();

        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        } else {
            $query->when($this->query, fn($q) => $q->where('titulo', 'like', "%{$this->query}%"))
                ->when($this->editoraId, fn($q) => $q->where('editora_id', $this->editoraId))
                ->when($this->autorId, fn($q) => $q->whereHas('autores', fn($q2) => $q2->where('id', $this->autorId)));
        }

        return $query->with(['editora', 'autores']);
    }

    public function headings(): array
    {
        return ['ID', 'Título', 'Editora', 'Autores', 'Preço', 'ISBN', 'Bibliografia'];
    }

    public function map($livro): array
    {
        return [
            $livro->id,
            $livro->titulo,
            $livro->editora?->nome ?? '-',
            $livro->autores->pluck('nome')->join(', '),
            $livro->preco,
            $livro->isbn,
            $livro->bibliografia,
        ];
    }
}
