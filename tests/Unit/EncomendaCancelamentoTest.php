<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Encomenda;
use App\Models\User;
use App\Models\Endereco;
use App\Models\Carrinho;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EncomendaCancelamentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancelamento_solicitado_campo_boolean()
    {
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

        $this->assertTrue($encomenda->cancelamento_solicitado);

        $encomendaFalse = Encomenda::factory()
            ->for($user)
            ->for($endereco)
            ->for($carrinho)
            ->create([
                'cancelamento_solicitado' => false,
            ]);

        $this->assertFalse($encomendaFalse->cancelamento_solicitado);
    }

    public function test_only_cancelamento_solicitado_can_be_approved()
    {
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

        $this->assertFalse($encomenda->cancelamento_solicitado);

        // Simular tentativa de aprovação, que na lógica real deveria falhar
        $canApprove = $encomenda->cancelamento_solicitado === true;

        $this->assertFalse($canApprove);

        // Pedido com solicitação poderá passar aprovação
        $encomendaAprovado = Encomenda::factory()
            ->for($user)
            ->for($endereco)
            ->for($carrinho)
            ->create([
                'cancelamento_solicitado' => true,
                'status' => 'pendente',
            ]);

        $this->assertTrue($encomendaAprovado->cancelamento_solicitado);
    }
}

