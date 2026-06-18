<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockPendenciaService;
use App\Services\Mock\MockOsService;

class FinanceiroController extends Controller
{
    public function __construct(
        private MockPendenciaService $pendenciaService,
        private MockOsService $osService,
    ) {}

    public function index()
    {
        $tenantId   = session('tenant_id', 1);
        $pendencias = $this->pendenciaService->all($tenantId);
        $hoje       = now()->toDateString();

        foreach ($pendencias as &$p) {
            if ($p['status'] !== 'negociado') {
                $p['status'] = $this->calcularStatus($p['parcelas'], $hoje);
            }
            $p['valor_pago'] = collect($p['parcelas'])
                ->whereNotNull('pago_em')
                ->sum('valor');
        }
        unset($p);

        $metricas = [
            'em_aberto'    => collect($pendencias)
                ->whereIn('status', ['pendente', 'parcial'])
                ->sum(fn ($p) => $p['valor_total'] - $p['valor_pago']),
            'vencido'      => collect($pendencias)
                ->where('status', 'vencido')
                ->sum(fn ($p) => $p['valor_total'] - $p['valor_pago']),
            'recebido_mes' => collect($pendencias)
                ->flatMap(fn ($p) => $p['parcelas'])
                ->filter(fn ($parc) => $parc['pago_em'] &&
                    str_starts_with($parc['pago_em'], now()->format('Y-m')))
                ->sum('valor'),
            'ativas'       => collect($pendencias)
                ->whereNotIn('status', ['pago'])
                ->count(),
        ];

        $osList = $this->osService->all($tenantId);

        return view('oficina.financeiro.index', compact('pendencias', 'metricas', 'osList'));
    }

    private function calcularStatus(array $parcelas, string $hoje): string
    {
        $pagas    = collect($parcelas)->whereNotNull('pago_em')->count();
        $total    = count($parcelas);
        $vencidas = collect($parcelas)
            ->whereNull('pago_em')
            ->filter(fn ($p) => $p['vencimento'] < $hoje)
            ->count();

        if ($pagas === $total) return 'pago';
        if ($pagas > 0)        return 'parcial';
        if ($vencidas > 0)     return 'vencido';
        return 'pendente';
    }
}
