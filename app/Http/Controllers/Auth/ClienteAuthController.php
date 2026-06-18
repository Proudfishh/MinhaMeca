<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockClienteService;
use Illuminate\Http\Request;

class ClienteAuthController extends Controller
{
    public function __construct(private MockClienteService $clientes) {}

    public function login(Request $request)
    {
        $this->setarSessao(
            ['id' => 1, 'nome' => 'Carlos Henrique Souza'],
            1,
            'Auto Center Premium'
        );

        return redirect()->route('cliente.veiculos.index');
    }

    public function selecionarOficina(Request $request)
    {
        $cliente = session('cliente_pendente');

        if (! $cliente) {
            return redirect()->route('login');
        }

        $tenantId   = (int) $request->input('tenant_id');
        $oficina    = collect($cliente['oficinas'])->firstWhere('id', $tenantId);

        if (! $oficina) {
            return redirect()->route('login');
        }

        $this->setarSessao($cliente, $oficina['id'], $oficina['nome']);
        session()->forget('cliente_pendente');

        return redirect()->route('cliente.veiculos.index');
    }

    private function setarSessao(array $cliente, int $tenantId, string $oficinaNome): void
    {
        session([
            'cliente_auth'    => true,
            'cliente_id'      => $cliente['id'],
            'cliente_nome'    => $cliente['nome'],
            'tenant_id'       => $tenantId,
            'oficina_nome'    => $oficinaNome,
        ]);
    }
}
