<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\AutorLivroController;
use App\Http\Controllers\HomeController;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\AutorLivro;


Route::get('/', [HomeController::class, 'index'])->name('welcome');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::resource('livros', LivroController::class);
Route::resource('autores', AutorController::class)->parameters([
    'autores' => 'autor',
]);
Route::resource('editoras', EditoraController::class);
Route::resource('autor-livro', AutorLivroController::class);
