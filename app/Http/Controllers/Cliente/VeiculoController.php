<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockOsService;

class VeiculoController extends Controller
{
    public function __construct(private MockOsService $osService) {}

    public function index()
    {
        $clienteId = (int) session('cliente_id', 0);
        $todasOs   = $this->osService->byCliente($clienteId);

        $ativas    = collect($todasOs)->filter(fn ($os) => empty($os['data_entrega_real']))->values()->all();
        $historico = collect($todasOs)->filter(fn ($os) => !empty($os['data_entrega_real']))->sortByDesc('data_entrega_real')->values()->all();

        $etapas = array_keys(MockOsService::ETAPAS);

        return view('cliente.veiculos.index', compact('ativas', 'historico', 'etapas'));
    }
}
