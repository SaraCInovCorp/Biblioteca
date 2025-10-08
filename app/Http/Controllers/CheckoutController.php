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
use Spatie\Activitylog\Models\Activity;

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
        \Log::info('Payload do formulário:', $request->all());
        $request->validate([
            'endereco_id' => 'required|exists:enderecos,id',
        ]);

        $user = auth()->user();

        $carrinho = Carrinho::where('user_id', $user->id)
                            ->where('status', 'ativo')
                            ->with('items.livro')
                            ->first();

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
                'carrinho_id' => $carrinho->id,
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
            \Log::info('Email do usuário para PaymentIntent: ' . $user->email);
            if ($encomenda->stripe_payment_intent_id) {
                $paymentIntent = PaymentIntent::update(
                    $encomenda->stripe_payment_intent_id,
                    [
                        'amount' => intval($total * 100),
                        'currency' => 'eur',
                        'metadata' => [
                            'encomenda_id' => $encomenda->id,
                            'user_id' => $user->id,
                        ],
                        'receipt_email' => $user->email,
                    ]
                );
            } else {
                $paymentIntent = PaymentIntent::create([
                    'amount' => intval($total * 100),
                    'currency' => 'eur',
                    'metadata' => [
                        'encomenda_id' => $encomenda->id,
                        'user_id' => $user->id,
                    ],
                    'receipt_email' => $user->email,
                ]);
                $encomenda->stripe_payment_intent_id = $paymentIntent->id;
                $encomenda->save();
            }
            \Log::info('Email do usuário para PaymentIntent: ' . $user->email);
            DB::commit();
            \Log::info('Antes do redirect', [
                'encomenda_id' => $encomenda->id ?? null,
                'client_secret' => $paymentIntent->client_secret ?? null,
            ]);
            if (empty($encomenda->id) || empty($paymentIntent->client_secret)) {
                return redirect()->route('checkout.index')->withErrors('Erro ao processar pagamento: dados incompletos!');
            }

            $encomenda->load('items');

            activity()
            ->causedBy(auth()->user())
            ->performedOn($encomenda)
            ->event('store')
            ->useLog('store-encomenda')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'user_executor_id' => auth()->id(),
                'user_for_id' => $encomenda->user_id,
                'total' => $encomenda->total,
                'status' => $encomenda->status,
                'payment_status' => $encomenda->payment_status,
                'items' => $encomenda->items->map(fn($item) => [
                    'id' => $item->id,
                    'livro_id' => $item->livro_id,
                    'quantidade' => $item->quantidade,
                    'preco_unitario' => $item->preco_unitario,
                ]),
            ])
            ->log('Encomenda criada');

            return redirect()->route('checkout.pagamento', [
                'encomenda' => $encomenda->id,
                'clientSecret' => $paymentIntent->client_secret
            ]);
        } catch (ApiErrorException $e) {
            DB::rollBack();

            activity()
                ->causedBy(auth()->user())
                ->event('error')
                ->useLog('error-checkout')
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'user_executor_id' => auth()->id(),
                    'error_type' => 'Stripe API error',
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getStripeCode(),
                    'error_param' => $e->getStripeParam(),
                    'error_json' => $e->getJsonBody(),
                ])
                ->log('Erro ao processar pagamento via Stripe');

            return redirect()->route('checkout.erro')
                            ->withErrors('Não conseguimos autenticar sua forma de pagamento. Escolha outra forma e tente novamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            activity()
                ->causedBy($user)
                ->event('error')
                ->useLog('error-checkout')
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'user_executor_id' => $user ? $user->id : null,
                    'error_message' => $e->getMessage(),
                    'error_trace' => $e->getTraceAsString(),
                ])
                ->log('Erro ao processar pedido no checkout');

            return redirect()->route('checkout.index')->withErrors('Erro ao processar pedido: ' . $e->getMessage());
        }
    }

    public function showPaymentPage(Request $request)
    {
        \Log::info('Info depois do redirect:', [
            'encomenda_id' => $request->encomenda ?? null,
            'paymentIntent_client_secret' => $request->clientSecret ?? null,
        ]);

        $clientSecret = $request->clientSecret;
        $encomendaId = $request->encomenda;
        
        $encomenda = Encomenda::with(['items.livro', 'endereco'])->find($encomendaId);

        if (!$encomenda) {
            abort(404, 'Encomenda não encontrada');
        }

        \Log::info('Info encomenda carregada:', [
            'encomenda' => $encomenda ?? null,
            'clientSecret' => $clientSecret ?? null,
        ]);

        return view('checkout.pagamento', compact('clientSecret', 'encomenda'));
    }

    public function success(Request $request)
    {
        $paymentIntentId = $request->get('payment_intent');
        $user = auth()->user();

        if ($paymentIntentId) {
            $encomenda = Encomenda::where('stripe_payment_intent_id', $paymentIntentId)->first();
            if ($encomenda) {
                $encomenda->payment_status = 'pago';
                $encomenda->status = 'finalizado';
                $encomenda->save();

                activity()
                    ->causedBy($user)
                    ->performedOn($encomenda)
                    ->event('finalizado')
                    ->useLog('finalizado-encomenda')
                    ->withProperties([
                        'ip' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'user_executor_id' => $user->id,
                        'user_for_id' => $encomenda->user_id,
                        'status_anterior' => $encomenda->getOriginal('status'),
                        'status_novo' => $encomenda->status,
                        'payment_status' => $encomenda->payment_status,
                        'total' => $encomenda->total,
                        'items' => $encomenda->items->map(fn($item) => [
                            'id' => $item->id,
                            'livro_id' => $item->livro_id,
                            'quantidade' => $item->quantidade,
                            'preco_unitario' => $item->preco_unitario,
                        ]),
                    ])
                    ->log('Compra finalizada e encomenda atualizada para finalizado');
            }
        }

        $carrinho = Carrinho::where('user_id', $user->id)
            ->where('status', 'ativo')
            ->first();
        if ($carrinho) {
            $carrinho->items()->delete(); 
            $carrinho->status = 'finalizado';
            $carrinho->save();
        }

        return view('checkout.sucesso');
    }

    public function error()
    {
        return view('checkout.erro');
    }

}
