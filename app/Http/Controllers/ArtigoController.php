<?php

namespace App\Http\Controllers;

use App\Models\Artigo;
use App\Models\Iva; // <-- Adicionar import
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect; // <-- Adicionar import

class ArtigoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Artigo::with('iva');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nome', 'like', "%{$searchTerm}%")
                    ->orWhere('referencia', 'like', "%{$searchTerm}%")
                    ->orWhere('descricao', 'like', "%{$searchTerm}%");
            });
        }

        $artigos = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Configuracoes/Artigos/Index', [
            'artigos' => $artigos,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // --- INÍCIO DA ALTERAÇÃO ---
        return Inertia::render('Configuracoes/Artigos/Create', [
            // Enviamos a lista de taxas de IVA ativas para o dropdown do formulário
            'ivas' => Iva::where('estado', 'ativo')->orderBy('taxa')->get(['id', 'nome', 'taxa']),
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
            // 'referencia' => 'required|...', // REMOVER ESTA LINHA
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'iva_id' => 'required|exists:ivas,id',
            'observacoes' => 'nullable|string',
            'estado' => 'required|in:ativo,inativo',
        ]);
        // --- FIM DA ALTERAÇÃO ---

        Artigo::create($validatedData);

        return Redirect::route('configuracoes.artigos.index')->with('success', 'Artigo criado com sucesso.');
    }
    // ... (o resto dos métodos continuam vazios por agora) ...
}
