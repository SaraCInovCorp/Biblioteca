<?php

use App\Models\Endereco;
use App\Models\Carrinho;
use App\Models\CarrinhoItem;
use App\Models\Encomenda;
use App\Models\EncomendaItem;
use App\Models\User;
use App\Models\Livro;
use Illuminate\Support\Facades\Schema;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('encomenda relations and items', function () {
    $user = User::factory()->create();
    $endereco = Endereco::factory()->for($user)->create();
    $carrinho = Carrinho::factory()->for($user)->create();

    $encomenda = Encomenda::factory()
        ->for($user)
        ->for($endereco)
        ->for($carrinho)
        ->create();

    $livros = Livro::factory()->count(2)->create();

    foreach ($livros as $livro) {
        EncomendaItem::factory()->for($encomenda)->for($livro)->create();
    }

    $encomenda->refresh();

    expect($encomenda->user->id)->toEqual($user->id);
    expect($encomenda->endereco->id)->toEqual($endereco->id);
    expect($encomenda->carrinho->id)->toEqual($carrinho->id);
    expect($encomenda->items)->toHaveCount(2);

    foreach ($encomenda->items as $item) {
        expect($livros->contains($item->livro))->toBeTrue();
    }
});