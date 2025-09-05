<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\PropostaController;
use App\Http\Controllers\ArtigoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('clientes', EntidadeController::class);
    // Rotas para Fornecedores 
    Route::resource('fornecedores', EntidadeController::class)
        ->parameters(['fornecedores' => 'fornecedor'])
        ->names('fornecedores');
    Route::resource('contactos', ContactoController::class);
    // Rotas para Propostas
    Route::resource('propostas', PropostaController::class);
    // --- Rotas de Configuração ---
    Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
        Route::resource('artigos', ArtigoController::class);
        // No futuro, outras rotas de configuração (IVA, Funções, etc.) virão aqui
    });
});
require __DIR__ . '/auth.php';
