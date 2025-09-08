<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookRequest;
use App\Models\BookRequestItem;

class BookRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BookRequest::factory()
            ->count(20)
            ->create()
            ->each(function($request) {
                // Cria entre 1 a 5 items para cada requisição
                $request->items()->saveMany(
                    BookRequestItem::factory()->count(rand(1,5))->make([
                        'book_request_id' => $request->id,
                    ])->all()
                );
            });
    }
}
