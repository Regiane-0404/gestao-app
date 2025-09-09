<?php

namespace App\Http\Controllers;

use App\Models\Proposta;
use App\Models\PropostaLinha;
use App\Models\Entidade;
use App\Models\Artigo;
use App\Models\Encomenda;
use App\Models\EncomendaLinha;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\DB;

class PropostaController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:ver_propostas')->only(['index', 'show']);
        $this->middleware('permission:criar_propostas')->only(['create', 'store']);
        $this->middleware('permission:editar_propostas')->only(['edit', 'update', 'adicionarLinha', 'removerLinha', 'atualizarLinha']);
        $this->middleware('permission:eliminar_propostas')->only('destroy');
        $this->middleware('permission:converter_propostas')->only('converterEmEncomenda');
    }
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
            // --- INÍCIO DA CORREÇÃO ---
            'encomendaExistenteId' => Encomenda::where('proposta_id', $proposta->id)->value('id'),
            // --- FIM DA CORREÇÃO ---
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
    // --- INÍCIO DA ALTERAÇÃO ---
    /**
     * Gera e descarrega o PDF de uma proposta.
     */
    public function downloadPDF(Proposta $proposta)
    {
        // 1. Carrega todas as relações necessárias de uma só vez para performance
        $proposta->load(['cliente', 'linhas']);

        // 2. Calcula os totais (Subtotal e Total IVA) para passar para a view
        $subtotal = $proposta->linhas->reduce(function ($carry, $linha) {
            return $carry + ($linha->quantidade * $linha->preco_unitario);
        }, 0);

        $totalIva = $proposta->linhas->reduce(function ($carry, $linha) {
            return $carry + ($linha->quantidade * $linha->preco_unitario * ($linha->taxa_iva / 100));
        }, 0);

        // 3. Prepara os dados para a view
        $data = [
            'proposta' => $proposta,
            'subtotal' => $subtotal,
            'totalIva' => $totalIva,
        ];

        // 4. Gera o PDF
        $pdf = PDF::loadView('pdf.proposta', $data);

        // 5. Devolve o PDF para o navegador para download
        // O nome do ficheiro será, por exemplo, "Proposta-1.pdf"
        return $pdf->download('Proposta-' . $proposta->id . '.pdf');
    }
    public function converterEmEncomenda(Proposta $proposta)
    {
        // 1. Validação da regra de negócio (só converte se estiver fechada)
        if ($proposta->estado !== 'fechado') {
            return Redirect::back()->with('error', 'Apenas propostas fechadas podem ser convertidas.');
        }

        // 2. Validação para impedir a re-conversão (segurança extra)
        $encomendaExistente = Encomenda::where('proposta_id', $proposta->id)->first();
        if ($encomendaExistente) {
            return Redirect::back()->with('error', 'Esta proposta já foi convertida em encomenda.');
        }

        // 3. Usar uma transação para garantir a integridade dos dados
        DB::transaction(function () use ($proposta) {
            // Criar o cabeçalho da encomenda
            $novaEncomenda = Encomenda::create([
                'entidade_id' => $proposta->entidade_id,
                'proposta_id' => $proposta->id,
                'data_encomenda' => now(), // A data da encomenda é a data da conversão
                'valor_total' => $proposta->valor_total,
                'estado' => 'fechado', // A encomenda já nasce "fechada"
            ]);

            // Copiar as linhas da proposta para a encomenda
            foreach ($proposta->linhas as $linha) {
                $novaEncomenda->linhas()->create([
                    'artigo_id' => $linha->artigo_id,
                    'referencia' => $linha->referencia,
                    'descricao' => $linha->descricao,
                    'quantidade' => $linha->quantidade,
                    'preco_unitario' => $linha->preco_unitario,
                    'taxa_iva' => $linha->taxa_iva,
                ]);
            }
        });

        // 4. Redirecionar DE VOLTA para a página da proposta com uma mensagem de sucesso
        return Redirect::back()->with('success', 'Proposta convertida em encomenda com sucesso.');
    }
}
