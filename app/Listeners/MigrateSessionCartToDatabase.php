<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Session;
use App\Models\Carrinho;

class MigrateSessionCartToDatabase
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        $sessionCart = Session::get('carrinho', []);

        if (empty($sessionCart)) {
            return;
        }

        $carrinho = Carrinho::firstOrCreate([
            'user_id' => $user->id,
            'status' => 'ativo',
        ]);

        foreach ($sessionCart as $livroId => $item) {
            $carrinhoItem = $carrinho->items()->where('livro_id', $livroId)->first();

            if ($carrinhoItem) {
                $carrinhoItem->quantidade += $item['quantidade'];
                $carrinhoItem->save();
            } else {
                $carrinho->items()->create([
                    'livro_id' => $livroId,
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco'],
                ]);
            }
        }

        Session::forget('carrinho');
    }
}
