<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrinho;
use App\Models\CarrinhoItem;
use App\Models\Livro;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class CarrinhoController extends Controller
{
    public function index()
    {
       if (auth()->check()) {
            $carrinho = Carrinho::where('user_id', auth()->id())->where('status', 'ativo')->first();
            $itens = $carrinho ? $carrinho->items()->with('livro')->get() : collect();
            $isAuthenticated = true;
        } else {
            $carrinhoSessao = session('carrinho', []);
            $itens = collect();

            foreach ($carrinhoSessao as $livroId => $item) {
                $livro = Livro::find($livroId);
                if ($livro) {
                    $itens->push((object)[
                        'id' => $livro->id,
                        'livro' => $livro,
                        'quantidade' => $item['quantidade'],
                        'preco_unitario' => $item['preco'],
                    ]);
                }
            }
            $isAuthenticated = false;
        }

        return view('carrinho.index', compact('itens', 'isAuthenticated'));
    }

    public function adicionar(Request $request, Livro $livro)
    {
        \Log::info('Adicionar ao carrinho: Livro ID ' . $livro->id);
        if (auth()->check()) {
            $carrinho = Carrinho::firstOrCreate([
                'user_id' => auth()->id(),
                'status' => 'ativo',
            ]);
            $item = $carrinho->items()->where('livro_id', $livro->id)->first();
            if ($item) {
                $item->quantidade++;
                $item->save();
            } else {
                $carrinho->items()->create([
                    'livro_id' => $livro->id,
                    'quantidade' => 1,
                    'preco_unitario' => $livro->preco,
                ]);
            }
        } else {
            $carrinhoSessao = session('carrinho', []);
            if (isset($carrinhoSessao[$livro->id])) {
                $carrinhoSessao[$livro->id]['quantidade']++;
            } else {
                $carrinhoSessao[$livro->id] = [
                    'titulo' => $livro->titulo,
                    'quantidade' => 1,
                    'preco' => $livro->preco,
                ];
            }
            session(['carrinho' => $carrinhoSessao]);
        }

        activity()
            ->causedBy(auth()->user())
            ->event('add-to-cart')
            ->useLog('carrinho')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'livro_id' => $livro->id,
                'livro_titulo' => $livro->titulo,
            ])
            ->log('Livro adicionado ao carrinho');

        return back()->with('message', 'Livro adicionado ao carrinho!');
    }

    public function atualizar(Request $request, CarrinhoItem $item)
    {
        $request->validate([
            'quantidade' => 'required|integer|min:1',
        ]);

        $item->quantidade = $request->input('quantidade');
        $item->save();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($item)
            ->event('update')
            ->useLog('carrinho')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'livro_id' => $item->livro_id,
                'quantidade' => $item->quantidade,
            ])
            ->log('Quantidade atualizada no carrinho (banco)');

        return redirect()->route('carrinho.index')
                        ->with('message', 'Quantidade atualizada com sucesso!');
    }

    public function atualizarSessao(Request $request)
    {
        $request->validate([
            'livro_id' => 'required|integer|exists:livros,id',
            'quantidade' => 'required|integer|min:1',
        ]);

        $livroId = $request->input('livro_id');
        $quantidade = $request->input('quantidade');

        $carrinho = session('carrinho', []);

        if (!isset($carrinho[$livroId])) {
            return back()->withErrors('Item não encontrado no carrinho.');
        }

        $carrinho[$livroId]['quantidade'] = $quantidade;

        session(['carrinho' => $carrinho]);

        activity()
            ->causedBy(auth()->check() ? auth()->user() : null)
            ->event('update')
            ->useLog('carrinho.sessao')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'livro_id' => $livroId,
                'quantidade' => $quantidade,
            ])
            ->log('Quantidade atualizada no carrinho (sessão)');

        return redirect()->route('carrinho.index')
                        ->with('message', 'Quantidade atualizada com sucesso!');
    }

    public function limparCarrinho(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            $carrinho = Carrinho::where('user_id', $user->id)
                ->where('status', 'ativo')
                ->first();

            if ($carrinho) {
                $carrinho->items()->delete();
            }
        } else {
            // Limpa carrinho na sessão para usuários não autenticados
            session()->forget('carrinho');
        }

        activity()
            ->causedBy($user)
            ->event('clear')
            ->useLog('carrinho')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ])
            ->log('Carrinho limpo');

        return redirect()->route('carrinho.index')
                        ->with('success', 'Carrinho limpo com sucesso.');
    }

    public function remover(CarrinhoItem $item)
    {
        $item->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($item)
            ->event('remove')
            ->useLog('carrinho')
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
                'livro_id' => $item->livro_id,
            ])
            ->log('Item removido do carrinho');

        return redirect()->route('carrinho.index')->with('message', 'Item removido do carrinho');
    }

    public function removerSessao(Request $request)
    {
        $request->validate([
            'livro_id' => 'required|integer|exists:livros,id',
        ]);

        $livroId = $request->input('livro_id');
        $carrinho = session('carrinho', []);

        if (isset($carrinho[$livroId])) {
            unset($carrinho[$livroId]);
            session(['carrinho' => $carrinho]);

            activity()
                ->causedBy(null)
                ->event('remove')
                ->useLog('carrinho.sessao')
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'livro_id' => $livroId,
                ])
                ->log('Item removido do carrinho (sessão)');

            return redirect()->route('carrinho.index')->with('message', 'Item removido do carrinho');
        }

        return redirect()->route('carrinho.index')->withErrors('Item não encontrado no carrinho');
    }

}
