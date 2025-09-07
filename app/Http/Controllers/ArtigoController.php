<?php

namespace App\Http\Controllers;

use App\Models\Artigo;
use App\Models\Iva;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class ArtigoController extends Controller
{
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

    public function create()
    {
        return Inertia::render('Configuracoes/Artigos/Create', [
            'ivas' => Iva::where('estado', 'ativo')->orderBy('taxa')->get(['id', 'nome', 'taxa']),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'iva_id' => 'required|exists:ivas,id',
            'observacoes' => 'nullable|string',
            'estado' => 'required|in:ativo,inativo',
        ]);

        Artigo::create($validatedData);

        return Redirect::route('configuracoes.artigos.index')->with('success', 'Artigo criado com sucesso.');
    }

    public function show(Artigo $artigo)
    {
        // Não iremos usar esta rota por agora.
    }

    // --- LÓGICA DE EDIÇÃO ---
    public function edit(Artigo $artigo)
    {
        return Inertia::render('Configuracoes/Artigos/Edit', [
            'artigo' => $artigo,
            'ivas' => Iva::where('estado', 'ativo')->orderBy('taxa')->get(['id', 'nome', 'taxa']),
        ]);
    }

    public function update(Request $request, Artigo $artigo)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'iva_id' => 'required|exists:ivas,id',
            'observacoes' => 'nullable|string',
            'estado' => 'required|in:ativo,inativo',
        ]);

        $artigo->update($validatedData);

        return Redirect::route('configuracoes.artigos.index')->with('success', 'Artigo atualizado com sucesso.');
    }

    // --- LÓGICA DE ELIMINAÇÃO ---
    public function destroy(Artigo $artigo)
    {
        $artigo->delete(); // Isto irá fazer um soft delete

        return Redirect::back()->with('success', 'Artigo eliminado com sucesso.');
    }
    public function search(Request $request)
    {
        // Valida se um termo de pesquisa foi enviado
        $request->validate([
            'term' => 'required|string|min:2',
        ]);

        $searchTerm = $request->term;

        $artigos = Artigo::with('iva') // Carrega a informação do IVA
            ->where('estado', 'ativo') // Apenas pesquisa em artigos ativos
            ->where(function ($query) use ($searchTerm) {
                $query->where('nome', 'like', "%{$searchTerm}%")
                    ->orWhere('referencia', 'like', "%{$searchTerm}%");
            })
            ->take(10) // Limita o número de resultados para performance
            ->get();

        // Devolve os resultados como JSON
        return response()->json($artigos);
    }
}
