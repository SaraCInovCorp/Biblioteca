<?php

namespace App\Exports;

use App\Models\Autor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithMapping;

class AutoresExport implements FromQuery, WithDrawings, WithMapping
{
    use Exportable;

    protected $query;
    protected $autores;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function query()
    {
        $this->autores = Autor::when($this->query, fn($q) => $q->where('nome', 'like', "%{$this->query}%"))->get();
        return $this->autores->toQuery();
    }

    public function map($autor): array
    {
        $imagemTexto = '';
        if ($autor->foto_url && Str::startsWith($autor->foto_url, ['http://','https://'])) {
            $imagemTexto = 'Imagem indisponível';
        }

        return [
            $autor->nome,
            $imagemTexto,
        ];
    }

    public function drawings()
    {
        $drawings = [];

        foreach ($this->autores as $i => $autor) {
            if ($autor->foto_url && !Str::startsWith($autor->foto_url, ['http://','https://'])) {
                $drawing = new Drawing();
                $drawing->setName('Foto ' . $autor->nome);
                $drawing->setDescription('Foto do autor');
                $drawing->setPath(storage_path('app/public/' . $autor->foto_url));
                $drawing->setHeight(50);
                $drawing->setCoordinates('B' . ($i + 2));
                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }
}