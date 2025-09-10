<?php

namespace App\Http\Controllers\Configuracoes;

use App\Http\Controllers\Controller;
use App\Models\ContactoFuncao;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class ContactoFuncaoController extends Controller
{
    public function index()
    {
        return Inertia::render('Configuracoes/Funcoes/Index', [
            'funcoes' => ContactoFuncao::orderBy('nome')->paginate(10),
            'funcoesArquivadas' => ContactoFuncao::onlyTrashed()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Configuracoes/Funcoes/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => ['required', 'string', 'max:255', Rule::unique('contacto_funcoes', 'nome')->whereNull('deleted_at')],
            'estado' => 'required|in:ativo,inativo',
        ]);

        ContactoFuncao::create($validatedData);

        return Redirect::route('configuracoes.contactos.funcoes.index')->with('success', 'Função de contacto criada com sucesso.');
    }

    public function edit(ContactoFuncao $funcoes_contacto) // O Laravel usa o nome do parâmetro da rota
    {
        return Inertia::render('Configuracoes/Funcoes/Edit', [
            'funcao' => $funcoes_contacto,
        ]);
    }

    public function update(Request $request, ContactoFuncao $funcoes_contacto)
    {
        $validatedData = $request->validate([
            'nome' => ['required', 'string', 'max:255', Rule::unique('contacto_funcoes')->ignore($funcoes_contacto->id)],
            'estado' => 'required|in:ativo,inativo',
        ]);

        $funcoes_contacto->update($validatedData);

        return Redirect::route('configuracoes.contactos.funcoes.index')->with('success', 'Função de contacto atualizada com sucesso.');
    }

    public function destroy(ContactoFuncao $funcoes_contacto)
    {
        $funcoes_contacto->delete(); // Soft delete
        return Redirect::back()->with('success', 'Função de contacto arquivada com sucesso.');
    }

    public function restore($id)
    {
        ContactoFuncao::withTrashed()->find($id)->restore();
        return Redirect::back()->with('success', 'Função de contacto restaurada com sucesso.');
    }
}
