<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookRequestSessionController extends Controller
{
    public function getBooks()
    {
        return response()->json(session('book_request', [
            'livros' => [],
            'data_inicio' => null,
            'data_fim' => null,
        ]));
    }

    public function storeBook(Request $request)
    {
        \Log::info('storeBook chamado', $request->all());
        $livro = $request->input('livro');

        if (!is_array($livro) || !isset($livro['id']) || !isset($livro['titulo'])) {
            return response()->json(['error' => 'Dados do livro inválidos'], 422);
        }

        $autor = $livro['autor'] ?? null; 

        $livroCorrigido = [
            'id' => $livro['id'],
            'titulo' => $livro['titulo'],
            'autor' => $autor,
        ];

        $bookRequestSession = session('book_request', [
            'livros' => [],
            'data_inicio' => null,
            'data_fim' => null,
        ]);

        $livros = collect($bookRequestSession['livros']);

        if ($livros->count() >= 3) {
            return response()->json(['error' => 'Você não pode requisitar mais de 3 livros simultaneamente.'], 422);
        }

        if (!$livros->contains('id', $livroCorrigido['id'])) {
            $livros->push($livroCorrigido);
            $bookRequestSession['livros'] = $livros->values()->all();
            session(['book_request' => $bookRequestSession]);
        }

        return response()->json(['message' => 'OK'], 200);
    }


    public function storeDates(Request $request)
    {
        $data = $request->only(['data_inicio', 'data_fim']);
        $bookRequestSession = session('book_request', [
            'livros' => [],
            'data_inicio' => null,
            'data_fim' => null,
        ]);
        $bookRequestSession['data_inicio'] = $data['data_inicio'];
        $bookRequestSession['data_fim'] = $data['data_fim'];
        session(['book_request' => $bookRequestSession]);
        return response()->json(['message' => 'Datas salvas']);
    }

    public function removeBook(Request $request)
    {
        $livroId = $request->input('id');
        $bookRequestSession = session('book_request', [
            'livros' => [],
            'data_inicio' => null,
            'data_fim' => null,
        ]);

        $livros = collect($bookRequestSession['livros'])
            ->reject(fn($item) => $item['id'] == $livroId)
            ->values()
            ->all();

        $bookRequestSession['livros'] = $livros;
        session(['book_request' => $bookRequestSession]);

        return response()->json(['message' => 'OK'], 200);
    }

    public function clearBooks(Request $request)
    {
        session()->forget('book_request');

        return response()->json(['message' => 'Limpo!']);
    }


}
