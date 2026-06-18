<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockClienteService;
use App\Services\Mock\MockOsService;
use App\Services\Mock\MockVeiculoService;

class ClienteController extends Controller
{
    public function __construct(
        private MockClienteService $clienteService,
        private MockOsService $osService,
        private MockVeiculoService $veiculoService,
    ) {}

    public function index()
    {
        $tenantId = session('tenant_id', 1);
        $clientes = $this->clienteService->all($tenantId);

        $clientes = array_map(function ($cliente) {
            $osDoCliente = $this->osService->byCliente($cliente['id']);
            $cliente['total_os'] = count($osDoCliente);
            $cliente['os_ativa'] = collect($osDoCliente)
                ->first(fn ($os) => $os['etapa_atual'] !== 'finalizacao');
            $cliente['total_veiculos'] = count($this->veiculoService->byCliente($cliente['id']));
            return $cliente;
        }, $clientes);

        return view('oficina.clientes.index', compact('clientes'));
    }

    public function show(int $id)
    {
        $cliente = $this->clienteService->find($id);
        abort_if(! $cliente, 404);

        $veiculos    = $this->veiculoService->byCliente($id);
        $osDoCliente = $this->osService->byCliente($id);

        usort($osDoCliente, fn ($a, $b) => strcmp($b['data_entrada'], $a['data_entrada']));

        // Inject OS ativa into each vehicle for badge display
        $osAtivasPorVeiculo = collect($osDoCliente)
            ->where('etapa_atual', '!=', 'finalizacao')
            ->keyBy('veiculo_id')
            ->all();

        $veiculos = array_map(function ($v) use ($osAtivasPorVeiculo) {
            $v['os_ativa'] = $osAtivasPorVeiculo[$v['id']] ?? null;
            return $v;
        }, $veiculos);

        $etapas = MockOsService::ETAPAS;

        return view('oficina.clientes.show', compact('cliente', 'veiculos', 'osDoCliente', 'etapas'));
    }
}
