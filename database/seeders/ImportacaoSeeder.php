<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Importacao;
use App\Models\LivroImportacao;
use App\Models\AutorImportacao;

class ImportacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): 
    {
        $importacao = Importacao::create([
            'user_id' => 1,
            'api' => 'seeder',
            'imported_at' => now(),
        ]);

        // Vincular livros à importação
        LivroImportacao::create([
            'importacao_id' => $importacao->id,
            'livro_id' => 1,
        ]);

        // Vincular autores à importação (exemplo)
        AutorImportacao::create([
            'importacao_id' => $importacao->id,
            'autor_id' => 1,
        ]);
        AutorImportacao::create([
            'importacao_id' => $importacao->id,
            'autor_id' => 2,
        ]);
    }
}
