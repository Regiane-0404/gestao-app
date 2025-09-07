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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // A relação no modelo Proposta chama-se 'cliente', não 'entidade'
        $query = Proposta::with('cliente'); // <-- CORRIGIDO AQUI

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                    // A relação no modelo Proposta chama-se 'cliente'
                    ->orWhereHas('cliente', function ($q) use ($searchTerm) { // <-- CORRIGIDO AQUI
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
        // --- INÍCIO DA ALTERAÇÃO ---
        return Inertia::render('Propostas/Create', [
            // Enviamos a lista de entidades que são clientes
            'clientes' => Entidade::where('is_cliente', true)->orderBy('nome')->get(['id', 'nome']),
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
            'validade' => 'required|date',
        ]);

        // Adicionamos o estado padrão
        $validatedData['estado'] = 'rascunho';

        // Criamos a proposta
        $proposta = Proposta::create($validatedData);

        // Redirecionamos para a página de EDIÇÃO para adicionar as linhas
        return Redirect::route('propostas.edit', $proposta->id)->with('success', 'Proposta criada. Adicione os artigos.');
        // --- FIM DA ALTERAÇÃO ---
    }
    public function edit(Proposta $proposta)
    {
        return Inertia::render('Propostas/Edit', [
            'proposta' => $proposta->load(['cliente', 'linhas']),
        ]);
    }

    public function update(Request $request, Proposta $proposta)
    {
        // Lógica de update do cabeçalho virá aqui (ex: mudar estado)
        // Por agora, vamos apenas redirecionar.
        return Redirect::route('propostas.index')->with('success', 'Proposta atualizada.');
    }

    public function destroy(Proposta $proposta)
    {
        $proposta->delete();
        return Redirect::back()->with('success', 'Proposta eliminada com sucesso.');
    }

    public function adicionarLinha(Request $request, Proposta $proposta)
    {
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
        $proposta = $propostaLinha->proposta;
        $propostaLinha->delete();
        $this->recalculateTotal($proposta);
        return Redirect::back()->with('success', 'Artigo removido da proposta.');
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
