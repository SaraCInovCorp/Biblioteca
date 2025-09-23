<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use App\Models\User;
use App\Models\BookRequest;
use App\Models\BookRequestItem;
use App\Models\Importacao;
use App\Models\BookReview;

class RelacionamentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_livro_belongs_to_editora_and_has_authors()
    {
        $user = User::factory()->create(); 
        $editora = Editora::factory()->create();
        $autores = Autor::factory()->count(3)->create();
        $livro = Livro::factory()->for($editora)->create();
        $livro->autores()->sync($autores->pluck('id'));

        $livro->refresh();

        $this->assertEquals($editora->id, $livro->editora->id);
        $this->assertCount(3, $livro->autores);
        foreach ($autores as $autor) {
            $this->assertTrue($livro->autores->contains($autor));
        }
    }

    public function test_importacao_has_livro_editora_autores()
    {
        $user = User::factory()->create();
        $editora = Editora::factory()->create();
        $autores = Autor::factory()->count(2)->create();
        $livro = Livro::factory()->for($editora)->create();

        $importacao = Importacao::create([
            'user_id' => $user->id,
            'api' => 'seeder',
            'imported_at' => now(),
        ]);

        $importacao->livros()->attach($livro->id);
        $importacao->editoras()->attach($editora->id);
        $importacao->autores()->sync($autores->pluck('id'));

        $importacao->refresh();

        $this->assertTrue($importacao->livros->contains($livro));
        $this->assertTrue($importacao->editoras->contains($editora));
        $this->assertEquals($user->id, $importacao->user->id);
        $this->assertCount(2, $importacao->autores);
        foreach ($autores as $autor) {
            $this->assertTrue($importacao->autores->contains($autor));
        }
    }

    public function test_bookrequest_belongs_to_user_and_has_items()
    {
        $user = User::factory()->create();
        $bookRequest = BookRequest::factory()->for($user)->create();

        $livros = Livro::factory()->count(2)->create();

        foreach ($livros as $livro) {
            BookRequestItem::factory()->for($bookRequest)->for($livro)->create();
        }

        $bookRequest->refresh();
        $this->assertEquals($user->id, $bookRequest->user->id);
        $this->assertCount(2, $bookRequest->items);

        foreach ($bookRequest->items as $item) {
            $this->assertTrue($livros->contains($item->livro));
        }
    }

    public function test_book_review_relations_and_states()
    {
        $user = User::factory()->create();
        $editora = Editora::factory()->create();
        $livro = Livro::factory()->for($editora)->create();

        $bookRequest = BookRequest::factory()->for($user)->create();
        $item = BookRequestItem::factory()->for($bookRequest)->for($livro)->create([
            'status' => 'entregue_ok',
            'data_real_entrega' => now(),
        ]);

        $review = BookReview::factory()->create([
            'book_request_item_id' => $item->id,
            'livro_id' => $livro->id,
            'user_id' => $user->id,
            'status' => 'suspenso',
            'review_text' => 'Este é um ótimo livro.',
            'admin_justification' => null,
        ]);

        $review->refresh();

        $this->assertEquals($item->id, $review->bookRequestItem->id);
        $this->assertEquals($livro->id, $review->livro->id);
        $this->assertEquals($user->id, $review->user->id);

        $this->assertEquals('suspenso', $review->status);

        $review->status = 'ativo';
        $review->save();

        $this->assertEquals('ativo', $review->fresh()->status);

        $this->assertTrue($livro->reviews->contains($review));

        $this->assertTrue($item->reviews->contains($review));
    }

}

