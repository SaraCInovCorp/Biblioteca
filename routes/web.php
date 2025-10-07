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
use App\Http\Controllers\LivroImportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\LivroWaitingListController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminPedidoController;
use App\Http\Controllers\PedidoController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use App\Models\Livro;
use App\Models\User;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\AutorLivro;
use App\Models\Bookrequest;

Route::get('/', [HomeController::class, 'index'])->name('welcome');

//Rotas Fortify para funcionalidades de redefinição de senha, verificação e 2FA, com middlewares de auditoria
Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])
    ->middleware(['auth'])
    ->name('password.confirmation.status');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware(['guest', 'log.password.reset.link'])
    ->name('password.email');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'log.verification.link'])
     ->name('verification.send');

// Route::middleware(['guest'])->group(function () {
//     // Exibe o formulário para o usuário digitar o código 2FA
//     Route::get('/two-factor-challenge', [TwoFactorChallengeController::class, 'create'])
//         ->name('two-factor.login');

//     // Processa o código 2FA (enviado via POST do formulário)
//     Route::post('/two-factor/login', [TwoFactorAuthenticatedSessionController::class, 'store'])
//         ->middleware('log.twofactor.authentication') // Aplica o middleware de logging aqui
//         ->name('two-factor.login.store');
// });

// Rotas protegidas para criação, edição, exclusão, etc.
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('dashboard', function () {
        return view('profile.show');
    })->name('dashboard');

    Route::get('admin/register', [AdminRegisterController::class, 'create'])->name('admin.register');
    Route::post('admin/register', [AdminRegisterController::class, 'store'])->name('admin.register.store');

    Route::get('google-books', [LivroController::class, 'pesquisarGoogleBooks'])->name('google-books.search');
    Route::get('editoras/check', [EditoraController::class, 'check'])->name('editoras.check');
    Route::get('autores/check', [AutorController::class, 'check'])->name('autores.check');
    
    // Rotas para importação via API Google Books
    Route::get('livros/import', [LivroImportController::class, 'showImportPage'])->name('livros.import.page');
    Route::post('livros/import', [LivroImportController::class, 'importSelected'])->name('livros.import.store');
    Route::get('livros/import/search', [LivroImportController::class, 'searchGoogleBooks'])->name('livros.import.search');
    Route::get('livros/importados/list', [LivroImportController::class, 'listaImportados'])->name('livros.importados.list');

    Route::get('waiting-list', [LivroWaitingListController::class, 'index'])->name('waiting-list.index');
    Route::post('livros/{livro}/waiting-list', [LivroWaitingListController::class, 'store'])->name('livro-waiting-list.store');
    
    Route::get('livros/search', [BookRequestController::class, 'searchLivros'])->name('livros.search');
    Route::get('livros/create', [LivroController::class, 'create'])->name('livros.create');
    Route::post('livros', [LivroController::class, 'store'])->name('livros.store');
    Route::get('livros/{livro}/edit', [LivroController::class, 'edit'])->name('livros.edit');
    Route::put('livros/{livro}', [LivroController::class, 'update'])->name('livros.update');
    Route::delete('livros/{livro}', [LivroController::class, 'destroy'])->name('livros.destroy');

    Route::get('autores/create', [AutorController::class, 'create'])->name('autores.create');
    Route::post('autores', [AutorController::class, 'store'])->name('autores.store');
    Route::get('autores/{autor}/edit', [AutorController::class, 'edit'])->name('autores.edit');
    Route::put('autores/{autor}', [AutorController::class, 'update'])->name('autores.update');
    Route::delete('autores/{autor}', [AutorController::class, 'destroy'])->name('autores.destroy');

    Route::get('editoras/create', [EditoraController::class, 'create'])->name('editoras.create');
    Route::post('editoras', [EditoraController::class, 'store'])->name('editoras.store');
    Route::get('editoras/{editora}/edit', [EditoraController::class, 'edit'])->name('editoras.edit');
    Route::put('editoras/{editora}', [EditoraController::class, 'update'])->name('editoras.update');
    Route::delete('editoras/{editora}', [EditoraController::class, 'destroy'])->name('editoras.destroy');
    
    // Route::resource('livros', LivroController::class)->except(['index', 'show']);
    // Route::resource('autores', AutorController::class)->parameters([
    //     'autores' => 'autor',
    // ])->except(['index', 'show']);
    // Route::resource('editoras', EditoraController::class)->except(['index', 'show']);
    
    //sessao requisicao
    Route::post('requisicoes/session', [BookRequestSessionController::class, 'storeBook'])->name('requisicoes.session.store');
    Route::get('requisicoes/session', [BookRequestSessionController::class, 'getBooks'])->name('requisicoes.session.get');
    Route::post('requisicoes/session/dates', [BookRequestSessionController::class, 'storeDates']);
    Route::delete('requisicoes/session', [BookRequestSessionController::class, 'removeBook']);
    Route::delete('requisicoes/session/clear', [BookRequestSessionController::class, 'clearBooks']);

    // Requisições protegidas
    Route::get('requisicoes', [BookRequestController::class, 'index'])->name('requisicoes.index');
    Route::get('requisicoes/create', [BookRequestController::class, 'create'])->name('requisicoes.create');
    Route::post('requisicoes', [BookRequestController::class, 'store'])->name('requisicoes.store');
    Route::get('requisicoes/{bookRequest}/edit', [BookRequestController::class, 'edit'])->name('requisicoes.edit');
    Route::get('requisicoes/{bookRequest}', [BookRequestController::class, 'show'])->name('requisicoes.show');
    Route::put('requisicoes/{bookRequest}', [BookRequestController::class, 'update'])->name('requisicoes.update');
    Route::delete('requisicoes/{bookRequest}', [BookRequestController::class, 'destroy'])->name('requisicoes.destroy');

    // Rotas para gerenciamento de reviews
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('reviews/bulk-update', [ReviewController::class, 'bulkUpdate'])->name('reviews.bulkUpdate');
    Route::get('reviews/{bookReview}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::get('reviews/{bookReview}', [ReviewController::class, 'show'])->name('reviews.show');
    Route::put('reviews/{bookReview}', [ReviewController::class, 'update'])->name('reviews.update');


    //pesquisa de usuario
    Route::get('users/search', [BookRequestController::class, 'searchUsers'])->name('users.search');
    Route::get('users/{user?}', [UserController::class, 'show'])->name('users.show');

    // Rotas para o checkout
    Route::get('checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('checkout/processar', [CheckoutController::class, 'processar'])->name('checkout.processar');
    Route::get('checkout/pagamento', [CheckoutController::class, 'showPaymentPage'])->name('checkout.pagamento');
    Route::get('checkout/sucesso', [CheckoutController::class, 'success'])->name('checkout.sucesso');
    Route::get('checkout/erro', [CheckoutController::class, 'error'])->name('checkout.erro');

    // Rotas para admin dos pedidos
    Route::get('/admin/pedidos', [AdminPedidoController::class, 'index'])->name('admin.pedidos.index');
    Route::get('/admin/pedidos/{pedido}', [AdminPedidoController::class, 'show'])->name('admin.pedidos.show');
    Route::post('admin/pedidos/{pedido}/aprovar-cancelamento', [AdminPedidoController::class, 'aprovarCancelamento'])->name('admin.pedidos.aprovar-cancelamento');
    Route::post('admin/pedidos/{pedido}/rejeitar-cancelamento', [AdminPedidoController::class, 'rejeitarCancelamento'])->name('admin.pedidos.rejeitar-cancelamento');
    Route::delete('admin/pedidos/{pedido}/cancelar', [AdminPedidoController::class, 'cancelar'])->name('admin.pedidos.cancelar');


    // Rotas para lista de pedidos para usuario
    Route::get('pedidos/meus-pedidos', [PedidoController::class, 'meusPedidos'])->name('pedidos.meus');
    Route::get('pedidos/meus-pedidos/{pedido}', [PedidoController::class, 'detalhePedido'])->name('pedidos.meus.detalhe');
    Route::delete('pedidos/{pedido}/cancelar', [PedidoController::class, 'cancelarPedido'])->name('pedidos.cancelar');
    Route::post('pedidos/{pedido}/solicitar-cancelamento', [PedidoController::class, 'solicitarCancelamento'])->name('pedidos.solicitar-cancelamento');

});

// Rotas públicas para livros, autores e editoras - apenas index e show
Route::get('livros', [LivroController::class, 'index'])->name('livros.index');
Route::get('/livros/{livro}/relacionados', [LivroController::class, 'buscarMaisRelacionados'])->name('livros.relacionados');
Route::get('livros/{livro}', [LivroController::class, 'show'])->name('livros.show');

Route::get('autores', [AutorController::class, 'index'])->name('autores.index');
Route::get('autores/{autor}', [AutorController::class, 'show'])->name('autores.show');

Route::get('editoras', [EditoraController::class, 'index'])->name('editoras.index');
Route::get('editoras/{editora}', [EditoraController::class, 'show'])->name('editoras.show');

 // Rotas para o carrinho
Route::get('carrinho', [CarrinhoController::class, 'index'])->name('carrinho.index');
Route::post('carrinho/limpar', [CarrinhoController::class, 'limparCarrinho'])->name('carrinho.limpar');
Route::post('carrinho/adicionar/{livro}', [CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar');
Route::post('carrinho/atualizar/{item}', [CarrinhoController::class, 'atualizar'])->name('carrinho.atualizar');
Route::post('carrinho/remover/{item}', [CarrinhoController::class, 'remover'])->name('carrinho.remover');

Route::get('livros/export/excel', [LivroController::class, 'exportExcel'])->name('livros.export.excel');
Route::get('livros/export/pdf', [LivroController::class, 'exportPdf'])->name('livros.export.pdf');

Route::get('editoras/export/excel', [EditoraController::class, 'exportExcel'])->name('editoras.export.excel');
Route::get('editoras/export/pdf', [EditoraController::class, 'exportPdf'])->name('editoras.export.pdf');

Route::get('autores/export/excel', [AutorController::class, 'exportExcel'])->name('autores.export.excel');
Route::get('autores/export/pdf', [AutorController::class, 'exportPdf'])->name('autores.export.pdf');

Route::get('importacoes/{id}/export/excel', [LivroImportController::class, 'exportExcelPorImportacao'])->name('importacoes.export.excel');
Route::get('importacoes/{id}/export/pdf', [LivroImportController::class, 'exportPdfPorImportacao'])->name('importacoes.export.pdf');

