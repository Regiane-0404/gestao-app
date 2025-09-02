<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class EntidadeController extends Controller
{
    public function index(Request $request)
    {
        // Determina se estamos a ver clientes ou fornecedores com base na rota
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
            'pageTitle' => $pageTitle, // Título dinâmico
            'routeName' => $routeName, // Rota dinâmica para pesquisa e botões
        ]);
    }
    public function create(Request $request)
    {
        // Determina se estamos a criar um cliente ou um fornecedor
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
            // Adicionar outras validações aqui conforme necessário
        ]);

        Entidade::create($validatedData);

        // Lógica de redirecionamento inteligente
        if ($request->input('is_fornecedor') && !$request->input('is_cliente')) {
            // Se for APENAS fornecedor, volta para a lista de fornecedores
            return Redirect::route('fornecedores.index')->with('success', 'Fornecedor criado com sucesso.');
        }

        // Para todos os outros casos (apenas cliente, ou ambos), volta para a lista de clientes
        return Redirect::route('clientes.index')->with('success', 'Entidade criada com sucesso.');
    }
}
