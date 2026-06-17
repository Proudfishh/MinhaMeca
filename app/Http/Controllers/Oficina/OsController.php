<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockOsService;
use App\Services\Mock\MockClienteService;
use App\Services\Mock\MockVeiculoService;
use Illuminate\Http\Request;

class OsController extends Controller
{
    public function __construct(
        private MockOsService $osService,
        private MockClienteService $clienteService,
        private MockVeiculoService $veiculoService,
    ) {}

    public function index()
    {
        $tenantId   = session('tenant_id', 1);
        $filaEtapas = $this->osService->byEtapa($tenantId);
        $etapas     = MockOsService::ETAPAS;
        $todasOs    = $this->osService->all($tenantId);

        return view('oficina.os.index', compact('filaEtapas', 'etapas', 'todasOs'));
    }

    public function show(string $id)
    {
        $os     = $this->osService->find($id);
        $etapas = MockOsService::ETAPAS;

        abort_if(! $os, 404);

        return view('oficina.os.show', compact('os', 'etapas'));
    }

    public function create()
    {
        $tenantId = session('tenant_id', 1);
        $clientes = $this->clienteService->all($tenantId);
        $veiculos = $this->veiculoService->all($tenantId);

        return view('oficina.os.create', compact('clientes', 'veiculos'));
    }

    public function store(Request $request)
    {
        $mockId = 'OS-2025-0051';

        return redirect()
            ->route('oficina.os.show', $mockId)
            ->with('success', "OS {$mockId} aberta com sucesso!");
    }
}
