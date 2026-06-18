<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockGarantiaService;

class GarantiaController extends Controller
{
    public function __construct(
        private MockGarantiaService $garantiaService,
    ) {}

    public function index()
    {
        $tenantId  = session('tenant_id', 1);
        $garantias = $this->garantiaService->all($tenantId);
        $hoje      = now()->toDateString();

        foreach ($garantias as &$g) {
            if ($g['os_retrabalho_id']) {
                $g['status'] = 'acionada';
            } else {
                $g['status'] = $this->calcularStatus($g['data_vencimento'], $hoje);
            }
            $g['dias_restantes'] = (int) now()->diffInDays($g['data_vencimento'], false);
        }
        unset($g);

        $metricas = [
            'ativas'    => collect($garantias)->where('status', 'ativa')->count(),
            'vencendo'  => collect($garantias)->where('status', 'vencendo')->count(),
            'expiradas' => collect($garantias)->where('status', 'expirada')->count(),
            'acionadas' => collect($garantias)->where('status', 'acionada')->count(),
        ];

        return view('oficina.garantias.index', compact('garantias', 'metricas'));
    }

    private function calcularStatus(string $dataVencimento, string $hoje): string
    {
        $diff = (int) now()->diffInDays($dataVencimento, false);
        if ($diff < 0)   return 'expirada';
        if ($diff <= 10) return 'vencendo';
        return 'ativa';
    }
}
