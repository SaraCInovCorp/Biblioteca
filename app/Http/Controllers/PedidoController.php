<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrinho;
use App\Models\Encomenda;
use App\Models\EncomendaItem;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    public function meusPedidos(Request $request)
    {
        $query = Encomenda::where('user_id', auth()->id());

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }
        if ($request->filled('livro')) {
            $query->whereHas('items.livro', function($q) use($request) {
                $q->where('titulo', 'like', '%' . $request->livro . '%');
            });
        }
        if ($request->filled('cancelamento_solicitado')) {
            $query->where('cancelamento_solicitado', $request->cancelamento_solicitado);
        }
        if ($request->filled('reembolso_aprovado')) {
            $query->where('reembolso_aprovado', $request->reembolso_aprovado);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('pedidos.meus-pedidos', compact('pedidos'));
    }


    public function detalhePedido(Encomenda $pedido)
    {
        $user = auth()->user();

        if ($pedido->user_id !== $user->id) {
            abort(403, 'Acesso não autorizado.');
        }

        $pedido->load('items.livro', 'endereco');

        return view('pedidos.detalhe-pedido', compact('pedido'));
    }

    public function cancelarPedido(Encomenda $pedido)
    {
        $user = auth()->user();

        if ($pedido->user_id !== $user->id) {
            abort(403, 'Não autorizado');
        }

        if ($pedido->status === 'pendente') {
            $pedido->status = 'cancelado';
            $pedido->save();

            return redirect()->route('pedidos.meus')->with('success', 'Pedido cancelado com sucesso.');
        }

        if (in_array($pedido->status, ['finalizado', 'pago'])) {
            $pedido->cancelamento_solicitado = true;
            $pedido->save();

            return redirect()->route('pedidos.meus')->with('info', 'Solicitação de cancelamento enviada para aprovação.');
        }

        return redirect()->route('pedidos.meus')->with('error', 'Não é possível cancelar esse pedido.');
    }

    public function solicitarCancelamento(Encomenda $pedido)
    {
        $user = auth()->user();

        if (in_array($pedido->status, ['cancelado', 'pendente'])) {
            return redirect()->back()->with('error', 'Não pode solicitar cancelamento para este pedido.');
        }

        if ($pedido->cancelamento_solicitado) {
            return redirect()->back()->with('info', 'Você já solicitou o cancelamento deste pedido.');
        }

        $pedido->cancelamento_solicitado = true;
        $pedido->save();

        return redirect()->back()->with('success', 'Solicitação de cancelamento enviada com sucesso.');
    }




}
