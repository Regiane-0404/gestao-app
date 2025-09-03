<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Entidade; // <-- Adicionar import
use App\Models\ContactoFuncao; // <-- Adicionar import
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect; // <-- Adicionar import

class ContactoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contacto::with(['entidade', 'funcao']);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nome', 'like', "%{$searchTerm}%")
                    ->orWhere('apelido', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhereHas('entidade', function ($q) use ($searchTerm) {
                        $q->where('nome', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $contactos = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Contactos/Index', [
            'contactos' => $contactos,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // --- INÍCIO DA ALTERAÇÃO ---
        return Inertia::render('Contactos/Create', [
            // Enviamos a lista de todas as entidades e funções para os dropdowns do formulário
            'entidades' => Entidade::orderBy('nome')->get(['id', 'nome']),
            'funcoes' => ContactoFuncao::where('estado', 'ativo')->orderBy('nome')->get(['id', 'nome']),
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
            'contacto_funcao_id' => 'nullable|exists:contacto_funcoes,id',
            'nome' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'telemovel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'consentimento_rgpd' => 'boolean',
            'observacoes' => 'nullable|string',
            'estado' => 'required|in:ativo,inativo',
        ]);

        Contacto::create($validatedData);

        return Redirect::route('contactos.index')->with('success', 'Contacto criado com sucesso.');
        // --- FIM DA ALTERAÇÃO ---
    }

    // ... (o resto dos métodos continuam iguais e vazios por agora) ...
}
