<?php

namespace App\Exports;

use App\Models\Editora;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithMapping;

class EditorasExport implements FromQuery, WithDrawings, WithMapping
{
    use Exportable;

    protected $query;
    protected $editoras;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function query()
    {
        $this->editoras = Editora::when($this->query, fn($q) => $q->where('nome', 'like', "%{$this->query}%"))->get();
        return $this->editoras->toQuery();
    }

    public function map($editora): array
    {
        // Se a imagem for URL externa, retorna texto "Imagem indisponível"
        if ($editora->logo_url && Str::startsWith($editora->logo_url, ['http://','https://'])) {
            $imagemTexto = 'Imagem indisponível';
        } else {
            $imagemTexto = ''; // deixará espaço vazio, imagem inserida via drawings()
        }

        return [
            $editora->nome,
            $imagemTexto,
        ];
    }

    public function drawings()
    {
        $drawings = [];

        foreach ($this->editoras as $i => $editora) {
            if ($editora->logo_url && !Str::startsWith($editora->logo_url, ['http://','https://'])) {
                $drawing = new Drawing();
                $drawing->setName('Logo ' . $editora->nome);
                $drawing->setDescription('Logo da editora');
                $drawing->setPath(storage_path('app/public/' . $editora->logo_url));
                $drawing->setHeight(50);
                // Coluna B (segunda coluna), ajuste linha i+2 (conta header)
                $drawing->setCoordinates('B' . ($i + 2));
                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }
}