<?php

use App\Http\Controllers\ArtigoController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropostaController;
use App\Http\Controllers\EncomendaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/*
|--------------------------------------------------------------------------
| Rotas Protegidas por Autenticação
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // --- Dashboard ---
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['verified'])->name('dashboard');

    // --- Perfil do Utilizador ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Módulo de Entidades ---
    Route::resource('clientes', EntidadeController::class);
    Route::resource('fornecedores', EntidadeController::class)
        ->parameters(['fornecedores' => 'fornecedor'])
        ->names('fornecedores');

    // --- Módulo de Contactos ---
    Route::resource('contactos', ContactoController::class);

    // --- Módulo de Propostas ---
    Route::resource('propostas', PropostaController::class);
    Route::resource('encomendas', EncomendaController::class);
    Route::post('propostas/{proposta}/linhas', [PropostaController::class, 'adicionarLinha'])->name('propostas.linhas.store');
    Route::delete('propostas/linhas/{propostaLinha}', [PropostaController::class, 'removerLinha'])->name('propostas.linhas.destroy');
    Route::patch('propostas/linhas/{propostaLinha}', [PropostaController::class, 'atualizarLinha'])->name('propostas.linhas.update');
    Route::get('propostas/{proposta}/pdf', [PropostaController::class, 'downloadPDF'])->name('propostas.pdf');
    Route::post('propostas/{proposta}/converter', [PropostaController::class, 'converterEmEncomenda'])->name('propostas.converter');

    // --- Módulo de Configurações ---
    Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
        // API para pesquisa de artigos
        Route::get('artigos/search', [ArtigoController::class, 'search'])->name('artigos.search');

        // CRUD de Artigos
        Route::resource('artigos', ArtigoController::class);

        // Outras rotas de configuração (IVA, Funções, etc.) virão aqui no futuro
    });
});

require __DIR__ . '/auth.php';
