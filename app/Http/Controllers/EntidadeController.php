<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class EntidadeController extends Controller
{
    public function index(Request $request)
    {
        $isFornecedores = $request->routeIs('fornecedores.index');
        $query = Entidade::query();

        if ($isFornecedores) {
            $query->where('is_fornecedor', true);
            $pageTitle = 'Lista de Fornecedores';
            $routeName = 'fornecedores.index';
        } else {
            $query->where('is_cliente', true);
            $pageTitle = 'Lista de Clientes';
            $routeName = 'clientes.index';
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nome', 'like', "%{$searchTerm}%")
                    ->orWhere('nif', 'like', "%{$searchTerm}%")
                    ->orWhere('nic', 'like', "%{$searchTerm}%");
            });
        }

        $entidades = $query->latest()->paginate(10)->withQueryString();

        // --- INÍCIO DA CORREÇÃO ---
        return Inertia::render('Entidades/Index', [
            'entidades' => $entidades,
            'filters' => $request->only(['search']),
            'pageTitle' => $pageTitle,
            'sourceRoute' => $routeName, // Padronizado para 'sourceRoute'
        ]);
        // --- FIM DA CORREÇÃO ---
    }

    public function create(Request $request)
    {
        $isFornecedores = $request->routeIs('fornecedores.create');
        return Inertia::render('Entidades/Create', [
            'isFornecedores' => $isFornecedores,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'nif' => 'nullable|string|max:20|unique:entidades,nif',
            'nic' => 'nullable|string|max:20|unique:entidades,nic',
            'email' => 'nullable|email|max:255',
            'telemovel' => 'nullable|string|max:20',
            'is_cliente' => 'boolean',
            'is_fornecedor' => 'boolean',
            'morada' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'localidade' => 'nullable|string|max:255',
            'pais' => 'nullable|string|max:255',
        ]);

        Entidade::create($validatedData);

        if ($request->input('is_fornecedor') && !$request->input('is_cliente')) {
            return Redirect::route('fornecedores.index')->with('success', 'Fornecedor criado com sucesso.');
        }
        return Redirect::route('clientes.index')->with('success', 'Entidade criada com sucesso.');
    }

    // ... (os outros métodos continuam iguais) ...

    public function edit(Request $request, Entidade $entidade)
    {
        return Inertia::render('Entidades/Edit', [
            // Passar o objeto diretamente é o correto. O problema estava na rota.
            'entidade' => $entidade,

            // Esta lógica para a rota de retorno está correta.
            'sourceRoute' => $request->routeIs('fornecedores.edit')
                ? 'fornecedores.index'
                : 'clientes.index',
        ]);
    }

    // ... (o método update continua igual) ...

    public function update(Request $request, Entidade $entidade)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'nif' => ['nullable', 'string', 'max:20', Rule::unique('entidades')->ignore($entidade->id)],
            'nic' => ['nullable', 'string', 'max:20', Rule::unique('entidades')->ignore($entidade->id)],
            'email' => 'nullable|email|max:255',
            'telemovel' => 'nullable|string|max:20',
            'is_cliente' => 'boolean',
            'is_fornecedor' => 'boolean',
            'morada' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'localidade' => 'nullable|string|max:255',
            'pais' => 'nullable|string|max:255',
        ]);

        $entidade->update($validatedData);

        if ($request->input('is_fornecedor') && !$request->input('is_cliente')) {
            return Redirect::route('fornecedores.index')->with('success', 'Fornecedor atualizado com sucesso.');
        }
        return Redirect::route('clientes.index')->with('success', 'Cliente atualizado com sucesso.');
    }
}
