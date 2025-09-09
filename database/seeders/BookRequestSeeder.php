<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookRequest;
use App\Models\BookRequestItem;
use App\Models\Livro;


class BookRequestSeeder extends Seeder
{
    public function run()
    {
        
        BookRequest::factory()->count(20)->create()->each(function ($bookRequest) {
            
            $livros = Livro::where('status', 'disponivel')->inRandomOrder()->take(rand(1,3))->get();

            foreach ($livros as $livro) {
                $bookRequest->items()->create([
                    'livro_id' => $livro->id,
                    'data_real_entrega' => null,
                    'dias_decorridos' => null,
                    'status' => 'realizada',
                ]);

                
                $livro->update(['status' => 'requisitado']);
            }
        });
    }
}
