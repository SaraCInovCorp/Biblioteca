<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');

        $livrosSorteados = Livro::inRandomOrder()->take(6)->get();

        $livrosFiltrados = null;
        if ($query) {
            $livrosFiltrados = Livro::query()
                ->where('titulo', 'like', "%{$query}%")
                ->orWhereHas('autores', fn($q) => $q->where('nome', 'like', "%{$query}%"))
                ->orWhereHas('editora', fn($q) => $q->where('nome', 'like', "%{$query}%"))
                ->paginate(12);
        }

        return view('welcome', compact('livrosSorteados', 'livrosFiltrados', 'query'));
    }

}

