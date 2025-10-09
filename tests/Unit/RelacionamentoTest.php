<?php

use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use App\Models\BookRequest;
use App\Models\BookRequestItem;
use App\Models\Importacao;
use App\Models\BookReview;
use App\Models\LivroWaitingList;
use App\Models\Endereco;
use App\Models\Carrinho;
use App\Models\CarrinhoItem;
use App\Models\Encomenda;
use App\Models\EncomendaItem;
use App\Models\UserDocument;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('livros table exists', function () {
    expect(Schema::hasTable('livros'))->toBeTrue();
});

test('livro belongs to editora and has authors', function () {
    $user = User::factory()->create();
    $editora = Editora::factory()->create();
    $autores = Autor::factory()->count(3)->create();
    $livro = Livro::factory()->for($editora)->create();
    $livro->autores()->sync($autores->pluck('id'));

    $livro->refresh();

    expect($livro->editora->id)->toEqual($editora->id);
    expect($livro->autores)->toHaveCount(3);
    foreach ($autores as $autor) {
        expect($livro->autores->contains($autor))->toBeTrue();
    }
});

test('importacao has livro editora autores', function () {
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

    expect($importacao->livros->contains($livro))->toBeTrue();
    expect($importacao->editoras->contains($editora))->toBeTrue();
    expect($importacao->user->id)->toEqual($user->id);
    expect($importacao->autores)->toHaveCount(2);
    foreach ($autores as $autor) {
        expect($importacao->autores->contains($autor))->toBeTrue();
    }
});

test('bookrequest belongs to user and has items', function () {
    $user = User::factory()->create();
    $bookRequest = BookRequest::factory()->for($user)->create();

    $livros = Livro::factory()->count(2)->create();

    foreach ($livros as $livro) {
        BookRequestItem::factory()->for($bookRequest)->for($livro)->create();
    }

    $bookRequest->refresh();
    expect($bookRequest->user->id)->toEqual($user->id);
    expect($bookRequest->items)->toHaveCount(2);

    foreach ($bookRequest->items as $item) {
        expect($livros->contains($item->livro))->toBeTrue();
    }
});

test('book review relations and states', function () {
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

    expect($review->bookRequestItem->id)->toEqual($item->id);
    expect($review->livro->id)->toEqual($livro->id);
    expect($review->user->id)->toEqual($user->id);

    expect($review->status)->toEqual('suspenso');

    $review->status = 'ativo';
    $review->save();

    expect($review->fresh()->status)->toEqual('ativo');

    expect($livro->reviews->contains($review))->toBeTrue();

    expect($item->reviews->contains($review))->toBeTrue();
});

test('livro waiting list relations and active scope', function () {
    $user = User::factory()->create();
    $livro = Livro::factory()->create();

    // Cria inscrição ativa
    $waiting = LivroWaitingList::create([
        'livro_id' => $livro->id,
        'user_id' => $user->id,
        'ativo' => true,
        'notificado_em' => null,
    ]);

    $waiting->refresh();

    // Relações
    expect($waiting->livro->id)->toEqual($livro->id);
    expect($waiting->user->id)->toEqual($user->id);

    // Testa inscrição ativa
    $active = LivroWaitingList::where('ativo', true)
        ->where('livro_id', $livro->id)
        ->where('user_id', $user->id)
        ->exists();

    expect($active)->toBeTrue();

    // Desativa inscrição
    $waiting->ativo = false;
    $waiting->notificado_em = now();
    $waiting->save();

    // Verifica que não está mais ativa
    $inactive = LivroWaitingList::where('ativo', true)
        ->where('livro_id', $livro->id)
        ->where('user_id', $user->id)
        ->exists();

    expect($inactive)->toBeFalse();
});

test('user has one document and fields are valid', function () {
    $user = User::factory()->create();

    $document = UserDocument::factory()->create([
        'user_id' => $user->id,
        'data_nascimento' => '1990-01-01',
        'tipo_documento' => 'CC',
        'numero_documento' => '12345678',
        'data_emissao' => '2015-01-01',
        'data_validade' => '2025-01-01',
        'entidade_emissora' => 'Conservatória Central',
        'nacionalidade' => 'Portugal',
        'genero' => 'Masculino',
    ]);

    $user->load('document');

    expect($user->document)->not->toBeNull();
    expect($user->document->id)->toEqual($document->id);
    expect($user->document->data_nascimento->toDateString())->toEqual('1990-01-01');
    expect($user->document->tipo_documento)->toEqual('CC');
    expect($user->document->numero_documento)->toEqual('12345678');
    expect($user->document->data_emissao->toDateString())->toEqual('2015-01-01');
    expect($user->document->data_validade->toDateString())->toEqual('2025-01-01');
    expect($user->document->entidade_emissora)->toEqual('Conservatória Central');
    expect($user->document->nacionalidade)->toEqual('Portugal');
    expect($user->document->genero)->toEqual('Masculino');
});

test('user can have multiple enderecos', function () {
    $user = User::factory()->create();
    $enderecos = Endereco::factory()->count(2)->create(['user_id' => $user->id]);

    $user->refresh();
    expect($user->enderecos)->toHaveCount(2);

    foreach ($enderecos as $endereco) {
        expect($user->enderecos->contains($endereco))->toBeTrue();
    }
});

test('carrinho belongs to user and has items', function () {
    $user = User::factory()->create();
    $carrinho = Carrinho::factory()->for($user)->create();
    $livros = Livro::factory()->count(3)->create();

    foreach ($livros as $livro) {
        CarrinhoItem::factory()->create([
            'carrinho_id' => $carrinho->id,
            'livro_id' => $livro->id,
        ]);
    }

    $carrinho->refresh();

    expect($carrinho->user->id)->toEqual($user->id);
    expect($carrinho->items)->toHaveCount(3);

    foreach ($carrinho->items as $item) {
        expect($livros->contains($item->livro))->toBeTrue();
    }
});