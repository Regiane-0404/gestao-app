<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class EncomendaController extends Controller
{
    public function index()
    {
        // Lógica de listagem de encomendas virá aqui.
        // Por agora, apenas retornamos a view.
        return Inertia::render('Encomendas/Index', [
            'encomendas' => Encomenda::with('cliente')->latest()->paginate(10),
        ]);
    }
    public function edit(Encomenda $encomenda)
    {
        // Carregamos as relações para mostrar na página de edição
        $encomenda->load(['cliente', 'linhas']);

        return Inertia::render('Encomendas/Edit', [
            'encomenda' => $encomenda,
        ]);
    }
    public function destroy(Encomenda $encomenda)
    {
        $encomenda->delete(); // Soft delete
        return Redirect::back()->with('success', 'Encomenda eliminada com sucesso.');
    }
    public function show(Encomenda $encomenda)
    {
        $encomenda->load(['cliente', 'linhas', 'proposta']);

        return Inertia::render('Encomendas/Show', [
            'encomenda' => $encomenda,
        ]);
    }
}
