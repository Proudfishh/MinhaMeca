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
        // Mock: apenas redireciona com mensagem de sucesso
        return redirect()->route('oficina.orcamentos.index')
            ->with('sucesso', 'Orçamento criado com sucesso!');
    }
}
