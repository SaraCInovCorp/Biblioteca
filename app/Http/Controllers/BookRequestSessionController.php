<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookRequestSessionController extends Controller
{
    public function getBooks()
    {
        return response()->json(session('book_request', []));
    }

    public function storeBook(Request $request)
    {
        $livro = $request->input('livro');
        $bookRequestSession = $request->session()->get('book_request', []);

        // Evitar duplicatas
        if (!collect($bookRequestSession)->contains('id', $livro['id'])) {
            $bookRequestSession[] = $livro;
            $request->session()->put('book_request', $bookRequestSession);
        }

        return response()->json(['message' => 'OK'], 200);
    }
}
