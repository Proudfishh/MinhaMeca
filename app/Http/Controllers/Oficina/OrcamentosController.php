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

        $clientes     = $this->clienteService->all($tenantId);
        $itensEstoque = $this->itensEstoqueComStatus($tenantId);

        return view('oficina.orcamentos.create', compact('clientes', 'itensEstoque'));
    }

    public function edit(int $id)
    {
        $orc = $this->orcamentosService->find($id);

        if (! $orc) {
            abort(404, 'Orçamento não encontrado.');
        }

        $tenantId = session('tenant_id', 1);

        $clientes     = $this->clienteService->all($tenantId);
        $itensEstoque = $this->itensEstoqueComStatus($tenantId);

        // Monta o estado inicial do wizard a partir do orçamento existente.
        $pecas    = [];
        $servicos = [];
        foreach ($orc['itens'] as $i => $it) {
            if (($it['tipo'] ?? 'peca') === 'peca') {
                $pecas[] = [
                    'id'     => 'orc-' . $i,
                    'nome'   => $it['descricao'],
                    'preco'  => $it['preco'],
                    'qtd'    => $it['qtd'],
                    'manual' => true,
                ];
            } else {
                $servicos[] = [
                    'desc'  => $it['descricao'],
                    'valor' => round($it['preco'] * $it['qtd'], 2),
                ];
            }
        }

        if ($orc['os_vinculada']) {
            $contexto = 'os';
        } elseif (! empty($orc['cliente'])) {
            $contexto = 'cliente';
        } elseif (! empty($orc['veiculo'])) {
            $contexto = 'veiculo';
        } else {
            $contexto = 'avulso';
        }

        $orcamento = [
            'id'           => $orc['id'],
            'codigo'       => $orc['codigo'],
            'status'       => $orc['status'],
            'contexto'     => $contexto,
            'cliente'      => $orc['cliente']
                ? ['id' => null, 'nome' => $orc['cliente'], 'telefone' => '']
                : null,
            'veiculo'      => $orc['veiculo']
                ? ['id' => null, 'placa' => $orc['placa'], 'modelo' => $orc['veiculo'], 'cliente' => $orc['cliente'] ?? '']
                : null,
            'os_vinculada' => $orc['os_vinculada'],
            'validade'     => $orc['validade'],
            'pecas'        => $pecas,
            'servicos'     => $servicos,
        ];

        return view('oficina.orcamentos.create', compact('clientes', 'itensEstoque', 'orcamento'));
    }

    private function itensEstoqueComStatus(int $tenantId): array
    {
        $itensEstoque = $this->estoqueService->all($tenantId);
        foreach ($itensEstoque as &$item) {
            $item['status'] = $item['quantidade'] <= 0
                ? 'sem_estoque'
                : ($item['quantidade'] <= $item['estoque_minimo'] ? 'baixo' : 'ok');
        }
        unset($item);

        return $itensEstoque;
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
