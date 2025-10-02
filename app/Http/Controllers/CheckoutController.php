<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Carrinho;
use App\Models\Encomenda;
use App\Models\EncomendaItem;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $carrinho = Carrinho::where('user_id', auth()->id())->where('status', 'ativo')->first();
        $itens = $carrinho ? $carrinho->items()->with('livro')->get() : collect();

        $enderecos = auth()->user()->enderecos()->get();

        // calcula o total
        $total = $itens->sum(function($item){
            return $item->quantidade * $item->preco_unitario;
        });

        return view('checkout.index', compact('itens', 'enderecos', 'total'));
    }

    public function processar(Request $request)
    {
        $request->validate([
            'endereco_id' => 'required|exists:enderecos,id',
        ]);
        
        $user = auth()->user();

        $carrinho = Carrinho::where('user_id', $user->id)->where('status', 'ativo')->with('items.livro')->first();

        if (!$carrinho || $carrinho->items->isEmpty()) {
            return redirect()->route('carrinho.index')->withErrors('Seu carrinho está vazio.');
        }

        DB::beginTransaction();

        try {
            $total = 0;
            foreach ($carrinho->items as $item) {
                $total += $item->quantidade * $item->preco_unitario;
            }

            $encomenda = Encomenda::create([
                'user_id' => $user->id,
                'endereco_id' => $request->endereco_id,
                'status' => 'pendente',
                'total' => $total,
                'payment_status' => 'pendente',
            ]);

            foreach ($carrinho->items as $item) {
                EncomendaItem::create([
                    'encomenda_id' => $encomenda->id,
                    'livro_id' => $item->livro_id,
                    'quantidade' => $item->quantidade,
                    'preco_unitario' => $item->preco_unitario,
                ]);
            }

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $paymentIntent = PaymentIntent::create([
                'amount' => intval($total * 100),
                'currency' => 'eur',
                'metadata' => [
                    'encomenda_id' => $encomenda->id,
                    'user_id' => $user->id,
                ],
            ]);

            $encomenda->stripe_payment_intent_id = $paymentIntent->id;
            $encomenda->save();

            DB::commit();

            return redirect()->route('checkout.pagamento', ['encomenda' => $encomenda->id, 'clientSecret' => $paymentIntent->client_secret]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout.index')->withErrors('Erro ao processar pedido: ' . $e->getMessage());
        }
    }

    public function showPaymentPage(Request $request)
    {
        $clientSecret = $request->clientSecret;
        return view('checkout.pagamento', compact('clientSecret'));
    }


}
