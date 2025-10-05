<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrinho;
use App\Models\Encomenda;
use App\Models\EncomendaItem;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Refund;
use App\Notifications\PedidoCancelamentoStatusNotification;
use App\Notifications\PedidoCanceladoPorAdminNotification;

class AdminPedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Encomenda::with(['user', 'items.livro']);

        if ($request->filled('data_pedido_de')) {
            $query->whereDate('created_at', '>=', $request->data_pedido_de);
        }
        if ($request->filled('data_pedido_ate')) {
            $query->whereDate('created_at', '<=', $request->data_pedido_ate);
        }
        if ($request->filled('livro')) {
            $query->whereHas('items.livro', function($q) use($request) {
                $q->where('titulo', 'like', '%' . $request->livro . '%');
            });
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('cancelamento_solicitado')) {
            $query->where('cancelamento_solicitado', $request->cancelamento_solicitado);
        }
        if ($request->filled('reembolso_aprovado')) {
            $query->where('reembolso_aprovado', $request->reembolso_aprovado);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(10);

        $users = User::orderBy('name')->get();

        return view('admin.pedidos.index', compact('pedidos', 'users'));
    }

    public function show(Encomenda $pedido)
    {
        $pedido->load('items.livro', 'endereco', 'user');
        return view('admin.pedidos.show', compact('pedido'));
    }

    public function aprovarCancelamento(Encomenda $pedido)
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Ação não autorizada.');
        }

        if (!$pedido->cancelamento_solicitado) {
            return redirect()->back()->with('error', 'Não há solicitação de cancelamento pendente.');
        }

        if ($pedido->status === 'finalizado' && $pedido->payment_status === 'pago' && $pedido->stripe_payment_intent_id) {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            try {
                Refund::create([
                    'payment_intent' => $pedido->stripe_payment_intent_id,
                    'reason' => 'requested_by_customer',
                ]);
                $pedido->reembolso_aprovado = true;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Erro ao processar o reembolso: ' . $e->getMessage());
            }
        }

        $pedido->status = 'cancelado';
        $pedido->cancelamento_solicitado = false;
        $pedido->save();

        $pedido->user->notify(new PedidoCancelamentoStatusNotification($pedido, true));

        return redirect()->back()->with('success', 'Cancelamento aprovado e reembolso realizado.');
    }

    public function rejeitarCancelamento(Encomenda $pedido)
    {
        $user = auth()->user();

        if ($user->role !== 'admin') {
            abort(403, 'Ação não autorizada.');
        }

        if (!$pedido->cancelamento_solicitado) {
            return redirect()->back()->with('error', 'Sem solicitação pendente.');
        }

        $pedido->cancelamento_solicitado = false;
        $pedido->save();

        $pedido->user->notify(new PedidoCancelamentoStatusNotification($pedido, false));

        return redirect()->back()->with('success', 'Solicitação de cancelamento rejeitada.');
    }

    public function cancelar(Encomenda $pedido)
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Ação não autorizada.');
        }

        if ($pedido->status !== 'cancelado') {
            $pedido->status = 'cancelado';
            $pedido->cancelamento_solicitado = false;
            $pedido->save();

            $pedido->user->notify(new PedidoCanceladoPorAdminNotification($pedido));
        }

        return redirect()->route('admin.pedidos.show', $pedido)->with('success', 'Pedido cancelado e usuário notificado.');
    }




}
