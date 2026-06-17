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
        $cliente = $this->clientes->findByCpfEmail(
            $request->input('cpf'),
            $request->input('email')
        );

        if (! $cliente) {
            return back()->withErrors(['cpf' => 'Dados não encontrados.']);
        }

        if (count($cliente['oficinas']) > 1) {
            session(['cliente_pendente' => $cliente]);
            return view('auth.selecionar-oficina', compact('cliente'));
        }

        session([
            'cliente_auth'  => true,
            'cliente_id'    => $cliente['id'],
            'cliente_nome'  => $cliente['nome'],
            'tenant_id'     => $cliente['oficinas'][0]['id'],
        ]);

        return redirect()->route('cliente.veiculos.index');
    }
}
