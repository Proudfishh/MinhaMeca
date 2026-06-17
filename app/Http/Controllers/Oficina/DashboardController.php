<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockOsService;
use App\Services\Mock\MockEstoqueService;

class DashboardController extends Controller
{
    public function __construct(
        private MockOsService $osService,
        private MockEstoqueService $estoqueService,
    ) {}

    public function index()
    {
        $tenantId = session('tenant_id', 1);
        $os       = $this->osService->all($tenantId);
        $estoque  = $this->estoqueService->all($tenantId);

        $metricas = [
            'abertas'         => collect($os)->whereIn('etapa_atual', ['checkin', 'diagnostico'])->count(),
            'em_andamento'    => collect($os)->whereIn('etapa_atual', ['pecas', 'servico', 'testes'])->count(),
            'finalizadas_hoje' => collect($os)->where('etapa_atual', 'finalizacao')->count(),
            'receita_mes'     => collect($os)->whereNotIn('etapa_atual', ['checkin'])->sum('total'),
        ];

        $filaEtapas = $this->osService->byEtapa($tenantId);

        $estoqueBaixo = collect($estoque)->filter(
            fn ($p) => $p['quantidade'] <= $p['estoque_minimo']
        )->values();

        $graficoMeses = [
            ['mes' => 'Jan', 'valor' => 8420],
            ['mes' => 'Fev', 'valor' => 9150],
            ['mes' => 'Mar', 'valor' => 7800],
            ['mes' => 'Abr', 'valor' => 11200],
            ['mes' => 'Mai', 'valor' => 10350],
            ['mes' => 'Jun', 'valor' => 4870],
        ];

        return view('oficina.dashboard.index', compact(
            'metricas', 'filaEtapas', 'os', 'estoqueBaixo', 'graficoMeses'
        ));
    }
}
