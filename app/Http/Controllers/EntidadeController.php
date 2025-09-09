<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class EntidadeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver_entidades')->only('index');
        $this->middleware('permission:criar_entidades')->only(['create', 'store']);
        $this->middleware('permission:editar_entidades')->only(['edit', 'update']);
        $this->middleware('permission:eliminar_entidades')->only('destroy');
    }
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

        return Inertia::render('Entidades/Index', [
            'entidades' => $entidades,
            'filters' => $request->only(['search']),
            'pageTitle' => $pageTitle,
            'sourceRoute' => $routeName,
        ]);
    }

    public function create(Request $request)
    {
        $isFornecedores = $request->routeIs('fornecedores.create');

        return Inertia::render('Entidades/Create', [ // <-- CORRETO
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

    public function edit(Request $request, $id)
    {
        $entidade = Entidade::findOrFail($id);

        return Inertia::render('Entidades/Edit', [
            'entidade' => $entidade,
            'sourceRoute' => $request->routeIs('fornecedores.edit') ? 'fornecedores.index' : 'clientes.index',
        ]);
    }

    public function update(Request $request, $id) // <-- MUDANÇA CRUCIAL AQUI
    {
        // 1. Carregamos manualmente a entidade que queremos atualizar.
        $entidade = Entidade::findOrFail($id);

        // 2. Validamos os dados que vêm do formulário (ignorando nif/nic).
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'morada' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'localidade' => 'nullable|string|max:255',
            'pais' => 'nullable|string|max:255',
            'telemovel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_cliente' => 'boolean',
            'is_fornecedor' => 'boolean',
        ]);

        // 3. Atualizamos a entidade que carregámos com os dados validados.
        $entidade->update($validatedData);

        // 4. Lógica de redirecionamento
        $isFornecedorOnly = $entidade->is_fornecedor && !$entidade->is_cliente;
        $targetRoute = $isFornecedorOnly ? 'fornecedores.index' : 'clientes.index';

        return Redirect::route($targetRoute)->with('success', 'Entidade atualizada com sucesso.');
    }
    public function destroy(Request $request, $id) // <-- MUDANÇA CRUCIAL AQUI
    {
        // 1. Carregamos manualmente a entidade que queremos eliminar.
        $entidade = Entidade::findOrFail($id);

        // 2. Agora que temos a certeza que temos o objeto correto, eliminamos.
        $entidade->delete();

        // 3. A lógica de redirecionamento continua a mesma.
        $referer = $request->headers->get('referer');
        $isFromFornecedores = str_contains($referer, '/fornecedores');

        $targetRoute = $isFromFornecedores ? 'fornecedores.index' : 'clientes.index';

        return Redirect::back()->with('success', 'Entidade eliminada com sucesso.');
    }
}
