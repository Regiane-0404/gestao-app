<?php

namespace App\Http\Controllers;

use App\Models\Proposta;
use App\Models\Entidade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;


class PropostaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // A relação no modelo Proposta chama-se 'cliente', não 'entidade'
        $query = Proposta::with('cliente'); // <-- CORRIGIDO AQUI

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                    // A relação no modelo Proposta chama-se 'cliente'
                    ->orWhereHas('cliente', function ($q) use ($searchTerm) { // <-- CORRIGIDO AQUI
                        $q->where('nome', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $propostas = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Propostas/Index', [
            'propostas' => $propostas,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        // --- INÍCIO DA ALTERAÇÃO ---
        return Inertia::render('Propostas/Create', [
            // Enviamos a lista de entidades que são clientes
            'clientes' => Entidade::where('is_cliente', true)->orderBy('nome')->get(['id', 'nome']),
        ]);
        // --- FIM DA ALTERAÇÃO ---
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // --- INÍCIO DA ALTERAÇÃO ---
        $validatedData = $request->validate([
            'entidade_id' => 'required|exists:entidades,id',
            'validade' => 'required|date',
        ]);

        // Adicionamos o estado padrão
        $validatedData['estado'] = 'rascunho';

        // Criamos a proposta
        $proposta = Proposta::create($validatedData);

        // Redirecionamos para a página de EDIÇÃO para adicionar as linhas
        return Redirect::route('propostas.edit', $proposta->id)->with('success', 'Proposta criada. Adicione os artigos.');
        // --- FIM DA ALTERAÇÃO ---
    }

    public function edit(Proposta $proposta)
    {
        return Inertia::render('Propostas/Edit', [
            // Usamos o load() para carregar a relação do cliente de forma eficiente
            'proposta' => $proposta->load('cliente'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proposta $proposta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposta $proposta)
    {
        //
    }
}
