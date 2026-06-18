<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockConfiguracaoService;
use Illuminate\Http\Request;

class ConfiguracoesController extends Controller
{
    public function __construct(private MockConfiguracaoService $configService) {}

    public function index(Request $request)
    {
        $config    = $this->configService->get(session('tenant_id', 1));
        $tabAtiva  = $request->query('tab', 'conta');

        return view('oficina.configuracoes.index', compact('config', 'tabAtiva'));
    }
}
