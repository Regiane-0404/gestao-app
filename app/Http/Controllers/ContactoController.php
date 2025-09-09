<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\ContactoFuncao;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class ContactoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver_contactos')->only('index');
        $this->middleware('permission:criar_contactos')->only(['create', 'store']);
        $this->middleware('permission:editar_contactos')->only(['edit', 'update']);
        $this->middleware('permission:eliminar_contactos')->only('destroy');
    }
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
        return Inertia::render('Contactos/Create', [
            'entidades' => Entidade::orderBy('nome')->get(['id', 'nome']),
            'funcoes' => ContactoFuncao::where('estado', 'ativo')->orderBy('nome')->get(['id', 'nome']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
    }

    /**
     * Display the specified resource.
     */
    public function show(Contacto $contacto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    // --- INÍCIO DA ALTERAÇÃO ---
    public function edit(Contacto $contacto)
    {
        // Carregamos o contacto e também as listas de entidades e funções para os dropdowns
        return Inertia::render('Contactos/Edit', [
            'contacto' => $contacto,
            'entidades' => Entidade::orderBy('nome')->get(['id', 'nome']),
            'funcoes' => ContactoFuncao::where('estado', 'ativo')->orderBy('nome')->get(['id', 'nome']),
        ]);
    }
    // --- FIM DA ALTERAÇÃO ---

    /**
     * Update the specified resource in storage.
     */
    // --- INÍCIO DA ALTERAÇÃO ---
    public function update(Request $request, Contacto $contacto)
    {
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

        $contacto->update($validatedData);

        return Redirect::route('contactos.index')->with('success', 'Contacto atualizado com sucesso.');
    }
    // --- FIM DA ALTERAÇÃO ---

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contacto $contacto)
    {
        // --- INÍCIO DA ALTERAÇÃO ---
        // O Route Model Binding já nos dá o contacto correto.
        // Como o modelo usa SoftDeletes, isto irá preencher a coluna 'deleted_at'.
        $contacto->delete();

        return Redirect::back()->with('success', 'Contacto eliminado com sucesso.');
        // --- FIM DA ALTERAÇÃO ---
    }
}
