<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\AutorLivroController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookRequestController;
use App\Http\Controllers\BookRequestSessionController;
use App\Http\Controllers\AdminRegisterController;
use App\Http\Controllers\UserController;
use App\Models\Livro;
use App\Models\User;
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

    Route::get('/admin/register', [AdminRegisterController::class, 'create'])->name('admin.register');
    Route::post('/admin/register', [AdminRegisterController::class, 'store'])->name('admin.register.store');
    
    Route::resource('livros', LivroController::class)->except(['index', 'show']);
    Route::resource('autores', AutorController::class)->parameters([
        'autores' => 'autor',
    ])->except(['index', 'show']);
    Route::resource('editoras', EditoraController::class)->except(['index', 'show']);
    
    //sessao requisicao
    Route::post('/requisicoes/session', [BookRequestSessionController::class, 'storeBook'])->name('requisicoes.session.store');
    Route::get('/requisicoes/session', [BookRequestSessionController::class, 'getBooks'])->name('requisicoes.session.get');
    Route::post('/requisicoes/session/dates', [BookRequestSessionController::class, 'storeDates']);
    Route::delete('/requisicoes/session', [BookRequestSessionController::class, 'removeBook']);
    Route::delete('/requisicoes/session/clear', [BookRequestSessionController::class, 'clearBooks']);

    // Requisições protegidas
    Route::get('requisicoes', [BookRequestController::class, 'index'])->name('requisicoes.index');
    Route::get('requisicoes/create', [BookRequestController::class, 'create'])->name('requisicoes.create');
    Route::post('requisicoes', [BookRequestController::class, 'store'])->name('requisicoes.store');
    Route::get('requisicoes/{bookRequest}/edit', [BookRequestController::class, 'edit'])->name('requisicoes.edit');
    Route::get('/requisicoes/{bookRequest}', [BookRequestController::class, 'show'])->name('requisicoes.show');
    Route::put('requisicoes/{bookRequest}', [BookRequestController::class, 'update'])->name('requisicoes.update');
    Route::delete('requisicoes/{bookRequest}', [BookRequestController::class, 'destroy'])->name('requisicoes.destroy');


    //pesquisa de usuario
    Route::get('/users/search', [BookRequestController::class, 'searchUsers'])->name('users.search');
    Route::get('/users/{user?}', [UserController::class, 'show'])->name('users.show');
    //pesquisa de livros
    Route::get('/livros/search', [BookRequestController::class, 'searchLivros'])->name('livros.search');

});

Route::get('livros/export/excel', [LivroController::class, 'exportExcel'])->name('livros.export.excel');
Route::get('livros/export/pdf', [LivroController::class, 'exportPdf'])->name('livros.export.pdf');

Route::get('editoras/export/excel', [EditoraController::class, 'exportExcel'])->name('editoras.export.excel');
Route::get('editoras/export/pdf', [EditoraController::class, 'exportPdf'])->name('editoras.export.pdf');

Route::get('autores/export/excel', [AutorController::class, 'exportExcel'])->name('autores.export.excel');
Route::get('autores/export/pdf', [AutorController::class, 'exportPdf'])->name('autores.export.pdf');

