<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockOsService;

class OsController extends Controller
{
    public function __construct(private MockOsService $osService) {}

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
        return view('oficina.os.create');
    }
}
