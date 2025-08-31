<?php

namespace App\Http\Controllers;

use App\Models\Entidade; // <-- ADICIONE ISTO
use Illuminate\Http\Request;
use Inertia\Inertia; // <-- ADICIONE ISTO

class EntidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Buscar as entidades que são 'cliente' ou 'ambos'
        $clientes = Entidade::where('tipo', 'cliente')
            ->orWhere('tipo', 'ambos')
            ->orderBy('nome') // Ordenar por nome
            ->get();

        // 2. Enviar os dados para a página Vue
        return Inertia::render('Entidades/Index', [
            'clientes' => $clientes,
        ]);
    }

    // ... (resto dos métodos vazios)
}
