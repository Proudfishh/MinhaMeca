<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockVeiculoService;
use App\Services\Mock\MockClienteService;
use App\Services\Mock\MockOsService;

class VeiculoController extends Controller
{
    public function __construct(
        private MockVeiculoService $veiculoService,
        private MockClienteService $clienteService,
        private MockOsService $osService,
    ) {}

    public function index()
    {
        $tenantId = session('tenant_id', 1);
        $veiculos = $this->veiculoService->all($tenantId);

        $veiculos = array_map(function ($v) {
            $cliente         = $this->clienteService->find($v['cliente_id']);
            $v['cliente']    = $cliente['nome'] ?? '—';
            $osDoVeiculo     = collect($this->osService->all(session('tenant_id', 1)))
                ->where('veiculo_id', $v['id']);
            $v['total_os']   = $osDoVeiculo->count();
            $v['os_ativa']   = $osDoVeiculo
                ->first(fn ($os) => empty($os['data_entrega_real']));
            return $v;
        }, $veiculos);

        $etapas = MockOsService::ETAPAS;

        return view('oficina.veiculos.index', compact('veiculos', 'etapas'));
    }

    public function show(int $id)
    {
        $veiculo = $this->veiculoService->find($id);
        abort_if(! $veiculo, 404);

        $cliente     = $this->clienteService->find($veiculo['cliente_id']);
        $osDoVeiculo = collect($this->osService->all(session('tenant_id', 1)))
            ->where('veiculo_id', $id)
            ->sortByDesc('data_entrada')
            ->values()
            ->all();

        $etapas     = MockOsService::ETAPAS;
        $totalGasto = collect($osDoVeiculo)->sum('total');
        $ultimaOS   = collect($osDoVeiculo)->first();

        return view('oficina.veiculos.show', compact('veiculo', 'cliente', 'osDoVeiculo', 'etapas', 'totalGasto', 'ultimaOS'));
    }
}
