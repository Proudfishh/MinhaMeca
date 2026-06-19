<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockOsService;

class OsController extends Controller
{
    public function __construct(private MockOsService $osService) {}

    public function show(string $id)
    {
        $clienteId = (int) session('cliente_id', 0);
        $os        = $this->osService->find($id);

        abort_if(! $os || $os['cliente_id'] !== $clienteId, 404);

        $etapas = MockOsService::ETAPAS;

        return view('cliente.os.show', compact('os', 'etapas'));
    }
}
