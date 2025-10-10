<?php

use App\Models\User;
use App\Models\BookRequest;
use App\Models\BookRequestItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lista apenas as requisições do utilizador autenticado', function () {
    $user1 = User::factory()->create(['role' => 'cidadao']);
    $user2 = User::factory()->create(['role' => 'cidadao']);

    BookRequest::factory()
        ->for($user1)
        ->has(BookRequestItem::factory()->count(2), 'items')
        ->state(['ativo' => true])
        ->create(['notas' => 'Nota ativa user1']);
    
    BookRequest::factory()
        ->for($user1)
        ->has(BookRequestItem::factory()->count(2), 'items')
        ->state(['ativo' => false])
        ->create(['notas' => 'Nota inativa user1']);

    BookRequest::factory()
        ->for($user2)
        ->has(BookRequestItem::factory()->count(2), 'items')
        ->count(2)
        ->create(['notas' => 'Requisição de outro usuário']);

    $this->actingAs($user1);

    $response = $this->get(route('requisicoes.index'));

    $response->assertStatus(200);

    $user1BookRequests = BookRequest::where('user_id', $user1->id)->get();

    foreach ($user1BookRequests as $bookRequest) {
       $response->assertSeeText($bookRequest->ativo ? 'Ativa' : 'Inativa');
    }

    $user2BookRequests = BookRequest::where('user_id', $user2->id)->get();

    foreach ($user2BookRequests as $bookRequest) {
        $response->assertDontSeeText($bookRequest->notas);
    }
});

it('filtra corretamente as requisições do utilizador autenticado', function () {
    $user = User::factory()->create(['role' => 'cidadao']);
    $this->actingAs($user);

    BookRequest::factory()
        ->for($user)
        ->has(BookRequestItem::factory()->count(2), 'items')
        ->count(3)
        ->create();

    $response = $this->get(route('requisicoes.index', ['filtro' => 'ativas']));
    $response->assertStatus(200);
});
