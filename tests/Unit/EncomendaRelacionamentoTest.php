<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Endereco;
use App\Models\Carrinho;
use App\Models\CarrinhoItem;
use App\Models\Encomenda;
use App\Models\EncomendaItem;
use App\Models\User;
use App\Models\Livro;         // << IMPORTAR ESSA CLASSE
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class EncomendaRelacionamentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_encomenda_relations_and_items()
    {
        $user = User::factory()->create();
        $endereco = Endereco::factory()->for($user)->create();

        $encomenda = Encomenda::factory()->for($user)->for($endereco)->create();

        $livros = Livro::factory()->count(2)->create();

        foreach ($livros as $livro) {
            EncomendaItem::factory()->for($encomenda)->for($livro)->create();
        }

        $encomenda->refresh();

        $this->assertEquals($user->id, $encomenda->user->id);
        $this->assertEquals($endereco->id, $encomenda->endereco->id);
        $this->assertCount(2, $encomenda->items);

        foreach ($encomenda->items as $item) {
            $this->assertTrue($livros->contains($item->livro));
        }
    }
}
