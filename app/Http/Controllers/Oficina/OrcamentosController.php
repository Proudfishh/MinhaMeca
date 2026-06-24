<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockOrcamentosService;
use App\Services\Mock\MockClienteService;
use App\Services\Mock\MockEstoqueService;
use Illuminate\Http\Request;

class OrcamentosController extends Controller
{
    public function __construct(
        private MockOrcamentosService $orcamentosService,
        private MockClienteService    $clienteService,
        private MockEstoqueService    $estoqueService,
    ) {}

    public function index()
    {
        $tenantId   = session('tenant_id', 1);
        $orcamentos = $this->orcamentosService->all($tenantId);

        $metricas = [
            'total'    => count($orcamentos),
            'pendente' => collect($orcamentos)->where('status', 'pendente')->count(),
            'aprovado' => collect($orcamentos)->where('status', 'aprovado')->count(),
            'valor'    => collect($orcamentos)->sum('total'),
        ];

        return view('oficina.orcamentos.index', compact('orcamentos', 'metricas'));
    }

    public function create()
    {
        $tenantId = session('tenant_id', 1);

        $clientes = $this->clienteService->all($tenantId);

        $itensEstoque = $this->estoqueService->all($tenantId);
        foreach ($itensEstoque as &$item) {
            $item['status'] = $item['quantidade'] <= 0
                ? 'sem_estoque'
                : ($item['quantidade'] <= $item['estoque_minimo'] ? 'baixo' : 'ok');
        }
        unset($item);

        return view('oficina.orcamentos.create', compact('clientes', 'itensEstoque'));
    }

    public function store(Request $request)
    {
        return redirect()->route('oficina.orcamentos.index')
            ->with('sucesso', 'Orçamento criado com sucesso!');
    }

    public function show(int $id)
    {
        $orc = $this->orcamentosService->find($id);

        if (! $orc) {
            abort(404, 'Orçamento não encontrado.');
        }

        $osAbertas = [
            ['id' => 'OS-2024-018', 'cliente' => 'João Silva',     'veiculo' => 'Honda Civic'],
            ['id' => 'OS-2024-019', 'cliente' => 'Maria Oliveira', 'veiculo' => 'Toyota Corolla'],
            ['id' => 'OS-2024-020', 'cliente' => 'Carlos Santos',  'veiculo' => 'VW Polo'],
        ];

        return view('oficina.orcamentos.show', compact('orc', 'osAbertas'));
    }

    public function update(Request $request, int $id)
    {
        return redirect()->route('oficina.orcamentos.show', $id)
            ->with('sucesso', 'Orçamento atualizado com sucesso!');
    }
}
