<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrinho;
use App\Models\CarrinhoItem;
use App\Models\Livro;
use Illuminate\Support\Facades\Auth;

class CarrinhoController extends Controller
{
    public function index()
    {
       if (auth()->check()) {
            $carrinho = Carrinho::where('user_id', auth()->id())->where('status', 'ativo')->first();
            $itens = $carrinho ? $carrinho->items()->with('livro')->get() : collect();
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
        }

        return view('carrinho.index', ['itens' => $itens]);
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

        return back()->with('message', 'Livro adicionado ao carrinho!');
    }

    public function atualizar(Request $request, CarrinhoItem $item)
    {
        $this->validate($request, [
            'quantidade' => 'required|integer|min:1',
        ]);

        $item->quantidade = $request->input('quantidade');
        $item->save();

        return redirect()->route('carrinho.index');
    }

    public function remover(CarrinhoItem $item)
    {
        $item->delete();

        return redirect()->route('carrinho.index');
    }
}
