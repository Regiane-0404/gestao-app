<?php

namespace App\Http\Controllers\Configuracoes;

use App\Http\Controllers\Controller;
use App\Models\Iva;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class IvaController extends Controller
{
    public function index()
    {
        return Inertia::render('Configuracoes/Iva/Index', [
            'ivas' => Iva::orderBy('taxa')->paginate(10),
            'ivasArquivadas' => Iva::onlyTrashed()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Configuracoes/Iva/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'taxa' => [
                'required',
                'numeric',
                'min:0',
                Rule::unique('ivas', 'taxa')->whereNull('deleted_at'),
            ],
            'estado' => 'required|in:ativo,inativo',
        ]);

        Iva::create($validatedData);

        return Redirect::route('configuracoes.ivas.index')->with('success', 'Taxa de IVA criada com sucesso.');
    }

    public function edit(Iva $iva)
    {
        return Inertia::render('Configuracoes/Iva/Edit', [
            'iva' => $iva,
        ]);
    }

    public function update(Request $request, Iva $iva)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'taxa' => ['required', 'numeric', 'min:0', Rule::unique('ivas')->ignore($iva->id)],
            'estado' => 'required|in:ativo,inativo',
        ]);

        $iva->update($validatedData);

        return Redirect::route('configuracoes.ivas.index')->with('success', 'Taxa de IVA atualizada com sucesso.');
    }

    public function destroy(Iva $iva)
    {
        $iva->delete(); // Soft delete
        return Redirect::back()->with('success', 'Taxa de IVA eliminada com sucesso.');
    }
    public function restore($id)
    {
        Iva::withTrashed()->find($id)->restore();
        return Redirect::back()->with('success', 'Taxa de IVA restaurada com sucesso.');
    }
}
