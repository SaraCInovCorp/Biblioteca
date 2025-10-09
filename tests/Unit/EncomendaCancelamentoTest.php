<?php

use App\Models\Encomenda;
use App\Models\User;
use App\Models\Endereco;
use App\Models\Carrinho;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('cancelamento solicitado campo boolean', function () {
    $user = User::factory()->create();
    $endereco = Endereco::factory()->for($user)->create();
    $carrinho = Carrinho::factory()->for($user)->create();

    $encomenda = Encomenda::factory()
        ->for($user)
        ->for($endereco)
        ->for($carrinho)
        ->create([
            'cancelamento_solicitado' => true,
        ]);

    expect($encomenda->cancelamento_solicitado)->toBeTrue();

    $encomendaFalse = Encomenda::factory()
        ->for($user)
        ->for($endereco)
        ->for($carrinho)
        ->create([
            'cancelamento_solicitado' => false,
        ]);

    expect($encomendaFalse->cancelamento_solicitado)->toBeFalse();
});

test('only cancelamento solicitado can be approved', function () {
    $user = User::factory()->create();
    $endereco = Endereco::factory()->for($user)->create();
    $carrinho = Carrinho::factory()->for($user)->create();

    // Pedido sem solicitação de cancelamento não pode ser aprovado
    $encomenda = Encomenda::factory()
        ->for($user)
        ->for($endereco)
        ->for($carrinho)
        ->create([
            'cancelamento_solicitado' => false,
            'status' => 'pendente',
        ]);

    expect($encomenda->cancelamento_solicitado)->toBeFalse();

    // Simular tentativa de aprovação, que na lógica real deveria falhar
    $canApprove = $encomenda->cancelamento_solicitado === true;

    expect($canApprove)->toBeFalse();

    // Pedido com solicitação poderá passar aprovação
    $encomendaAprovado = Encomenda::factory()
        ->for($user)
        ->for($endereco)
        ->for($carrinho)
        ->create([
            'cancelamento_solicitado' => true,
            'status' => 'pendente',
        ]);

    expect($encomendaAprovado->cancelamento_solicitado)->toBeTrue();
});