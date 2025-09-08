<?php

namespace App\Http\Controllers;

use App\Models\Proposta;
use App\Models\PropostaLinha;
use App\Models\Entidade;
use App\Models\Artigo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class PropostaController extends Controller
{
    public function index(Request $request)
    {
        $query = Proposta::with('cliente');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                    ->orWhereHas('cliente', function ($q) use ($searchTerm) {
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
        return Inertia::render('Propostas/Create', [
            'clientes' => Entidade::where('is_cliente', true)->orderBy('nome')->get(['id', 'nome']),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'entidade_id' => 'required|exists:entidades,id',
            'validade' => 'required|date',
        ]);

        $validatedData['estado'] = 'rascunho';

        $proposta = Proposta::create($validatedData);

        return Redirect::route('propostas.edit', $proposta->id)->with('success', 'Proposta criada. Adicione os artigos.');
    }

    public function edit(Proposta $proposta)
    {
        return Inertia::render('Propostas/Edit', [
            'proposta' => $proposta->load(['cliente', 'linhas']),
        ]);
    }

    public function update(Request $request, Proposta $proposta)
    {
        if ($proposta->estado === 'fechado') {
            abort(403, 'Não é possível alterar uma proposta fechada.');
        }

        $validatedData = $request->validate([
            'estado' => 'sometimes|in:rascunho,fechado',
            'validade' => 'sometimes|required|date',
        ]);

        if (isset($validatedData['estado']) && $validatedData['estado'] === 'fechado' && is_null($proposta->data_proposta)) {
            $validatedData['data_proposta'] = now();
        }

        $proposta->update($validatedData);

        // --- INÍCIO DA CORREÇÃO ---
        // Usamos o helper de sessão diretamente para garantir que a mensagem é gravada.
        session()->flash('success', 'As alterações foram guardadas.');


        return Redirect::back();
        // --- FIM DA CORREÇÃO ---
    }
    public function destroy(Proposta $proposta)
    {
        // Proteção opcional
        // if ($proposta->estado === 'fechado') {
        //     abort(403, 'Não é possível eliminar uma proposta fechada.');
        // }
        $proposta->delete();
        return Redirect::back()->with('success', 'Proposta eliminada com sucesso.');
    }

    public function adicionarLinha(Request $request, Proposta $proposta)
    {
        // --- INÍCIO DA PROTEÇÃO ---
        if ($proposta->estado === 'fechado') {
            abort(403, 'Não é possível adicionar artigos a uma proposta fechada.');
        }
        // --- FIM DA PROTEÇÃO ---

        $validatedData = $request->validate([
            'artigo_id' => 'required|exists:artigos,id',
        ]);

        $artigo = Artigo::with('iva')->findOrFail($validatedData['artigo_id']);

        $proposta->linhas()->create([
            'artigo_id' => $artigo->id,
            'referencia' => $artigo->referencia,
            'descricao' => $artigo->nome,
            'quantidade' => 1,
            'preco_unitario' => $artigo->preco,
            'taxa_iva' => $artigo->iva->taxa,
        ]);

        $this->recalculateTotal($proposta);

        return Redirect::back()->with('success', 'Artigo adicionado à proposta.');
    }

    public function removerLinha(PropostaLinha $propostaLinha)
    {
        // --- INÍCIO DA PROTEÇÃO ---
        if ($propostaLinha->proposta->estado === 'fechado') {
            abort(403, 'Não é possível remover artigos de uma proposta fechada.');
        }
        // --- FIM DA PROTEÇÃO ---

        $proposta = $propostaLinha->proposta;
        $propostaLinha->delete();
        $this->recalculateTotal($proposta);
        return Redirect::back()->with('success', 'Artigo removido da proposta.');
    }

    public function atualizarLinha(Request $request, PropostaLinha $propostaLinha)
    {
        // --- INÍCIO DA PROTEÇÃO ---
        if ($propostaLinha->proposta->estado === 'fechado') {
            abort(403, 'Não é possível alterar artigos de uma proposta fechada.');
        }
        // --- FIM DA PROTEÇÃO ---

        $validatedData = $request->validate([
            'quantidade' => 'required|numeric|min:0.01',
        ]);

        $propostaLinha->update($validatedData);

        $this->recalculateTotal($propostaLinha->proposta);

        return Redirect::back()->with('success', 'Linha da proposta atualizada.');
    }

    private function recalculateTotal(Proposta $proposta)
    {
        $total = $proposta->linhas()->get()->reduce(function ($carry, $linha) {
            $totalLinha = $linha->quantidade * $linha->preco_unitario * (1 + $linha->taxa_iva / 100);
            return $carry + $totalLinha;
        }, 0);

        $proposta->update(['valor_total' => $total]);
    }
}
