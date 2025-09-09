<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\AutorLivroController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookRequestController;
use App\Http\Controllers\BookRequestSessionController;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\AutorLivro;
use App\Models\Bookrequest;

Route::get('/', [HomeController::class, 'index'])->name('welcome');

// Rotas públicas para livros, autores e editoras - apenas index e show
Route::get('livros/search', [BookRequestController::class, 'searchLivros'])->name('livros.search');
Route::get('livros', [LivroController::class, 'index'])->name('livros.index');
Route::get('livros/{livro}', [LivroController::class, 'show'])->name('livros.show');

Route::get('autores', [AutorController::class, 'index'])->name('autores.index');
Route::get('autores/{autor}', [AutorController::class, 'show'])->name('autores.show');

Route::get('editoras', [EditoraController::class, 'index'])->name('editoras.index');
Route::get('editoras/{editora}', [EditoraController::class, 'show'])->name('editoras.show');



// Rotas protegidas para criação, edição, exclusão, etc.
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::resource('livros', LivroController::class)->except(['index', 'show']);
    Route::resource('autores', AutorController::class)->parameters([
        'autores' => 'autor',
    ])->except(['index', 'show']);
    Route::resource('editoras', EditoraController::class)->except(['index', 'show']);
    // Requisições protegidas
    Route::get('requisicoes/create', [BookRequestController::class, 'create'])->name('requisicoes.create');
    Route::post('requisicoes', [BookRequestController::class, 'store'])->name('requisicoes.store');

    //pesquisa de usuario
    Route::get('/users/search', [BookRequestController::class, 'searchUsers'])->name('users.search');
    //pesquisa de livros
    Route::get('/livros/search', [BookRequestController::class, 'searchLivros'])->name('livros.search');
    Route::post('/requisicoes/session', [BookRequestSessionController::class, 'storeBook'])->name('requisicoes.session.store');
    Route::get('/requisicoes/session', [BookRequestSessionController::class, 'getBooks'])->name('requisicoes.session.get');
});

Route::get('livros/export/excel', [LivroController::class, 'exportExcel'])->name('livros.export.excel');
Route::get('livros/export/pdf', [LivroController::class, 'exportPdf'])->name('livros.export.pdf');

Route::get('editoras/export/excel', [EditoraController::class, 'exportExcel'])->name('editoras.export.excel');
Route::get('editoras/export/pdf', [EditoraController::class, 'exportPdf'])->name('editoras.export.pdf');

Route::get('autores/export/excel', [AutorController::class, 'exportExcel'])->name('autores.export.excel');
Route::get('autores/export/pdf', [AutorController::class, 'exportPdf'])->name('autores.export.pdf');

