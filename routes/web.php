<?php


use App\Http\Controllers\ArtigoController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropostaController;
use App\Http\Controllers\EncomendaController;
use App\Http\Controllers\Gestao\RoleController;
use App\Http\Controllers\Gestao\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Configuracoes\IvaController; // Adicionar import
use App\Http\Controllers\Configuracoes\ContactoFuncaoController; // Adicionar import

use Inertia\Inertia;

// ... (o resto do ficheiro continua igual)

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () { /* ... */
});

/*
|--------------------------------------------------------------------------
| Rotas Protegidas por Autenticação
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // --- Dashboard ---
    Route::get('/dashboard', function () { /* ... */
    })->middleware(['verified'])->name('dashboard');

    // --- Perfil do Utilizador ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Módulos Principais ---
    Route::resource('clientes', EntidadeController::class);
    Route::resource('fornecedores', EntidadeController::class)->parameters(['fornecedores' => 'fornecedor'])->names('fornecedores');
    Route::resource('contactos', ContactoController::class);
    Route::resource('propostas', PropostaController::class);
    Route::resource('encomendas', EncomendaController::class);
    Route::post('propostas/{proposta}/linhas', [PropostaController::class, 'adicionarLinha'])->name('propostas.linhas.store');
    Route::delete('propostas/linhas/{propostaLinha}', [PropostaController::class, 'removerLinha'])->name('propostas.linhas.destroy');
    Route::patch('propostas/linhas/{propostaLinha}', [PropostaController::class, 'atualizarLinha'])->name('propostas.linhas.update');
    Route::get('propostas/{proposta}/pdf', [PropostaController::class, 'downloadPDF'])->name('propostas.pdf');
    Route::post('propostas/{proposta}/converter', [PropostaController::class, 'converterEmEncomenda'])->name('propostas.converter');

    // --- Módulo de Configurações ---
    Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
        Route::get('artigos/search', [ArtigoController::class, 'search'])->name('artigos.search');
        Route::resource('artigos', ArtigoController::class);
        Route::resource('ivas', IvaController::class);
        Route::resource('funcoes-contacto', ContactoFuncaoController::class)->names('contactos.funcoes');
        Route::post('ivas/{id}/restore', [IvaController::class, 'restore'])->name('ivas.restore');
         Route::resource('funcoes-contacto', ContactoFuncaoController::class)->names('contactos.funcoes');
        Route::post('funcoes-contacto/{id}/restore', [ContactoFuncaoController::class, 'restore'])->name('contactos.funcoes.restore');
    });

    // --- INÍCIO DA CORREÇÃO ---
    // Módulo de Gestão de Acessos (agora ao mesmo nível que Configurações)
    Route::prefix('gestao')->name('gestao.')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('utilizadores', UserController::class);
    });
    // --- FIM DA CORREÇÃO ---
});

require __DIR__ . '/auth.php';
