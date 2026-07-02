<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Services\Mock\MockOsService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function __construct(private MockOsService $osService) {}

    public function index()
    {
        $tenantId = session('tenant_id', 1);

        $clientes = Cliente::where('tenant_id', $tenantId)
            ->withCount('veiculos')
            ->orderBy('nome')
            ->get()
            ->map(function (Cliente $cliente) {
                $osDoCliente = $this->osService->byCliente($cliente->id);
                $cliente->total_os       = count($osDoCliente);
                $cliente->os_ativa       = collect($osDoCliente)->first(fn ($os) => $os['etapa_atual'] !== 'finalizacao');
                $cliente->total_veiculos = $cliente->veiculos_count;

                return $cliente;
            });

        return view('oficina.clientes.index', compact('clientes'));
    }

    public function store(Request $request)
    {
        $tenantId = session('tenant_id', 1);

        $data = $request->validate([
            'tipo'         => ['required', Rule::in(['pf', 'pj'])],
            'nome'         => ['required', 'string', 'max:255'],
            'cpf'          => ['required_if:tipo,pf', 'nullable', 'string', 'max:20'],
            'cnpj'         => ['required_if:tipo,pj', 'nullable', 'string', 'max:20'],
            'nome_contato' => ['nullable', 'string', 'max:255'],
            'telefone'     => ['required', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:255'],
            'cep'          => ['nullable', 'string', 'max:10'],
            'logradouro'   => ['nullable', 'string', 'max:255'],
            'numero'       => ['nullable', 'string', 'max:20'],
            'complemento'  => ['nullable', 'string', 'max:255'],
            'bairro'       => ['nullable', 'string', 'max:255'],
            'cidade'       => ['nullable', 'string', 'max:255'],
            'uf'           => ['nullable', 'string', 'max:2'],
        ]);

        $data['cpf']  = $data['tipo'] === 'pf' ? ($data['cpf'] ?? null) : null;
        $data['cnpj'] = $data['tipo'] === 'pj' ? ($data['cnpj'] ?? null) : null;
        $data['uf']   = ! empty($data['uf']) ? strtoupper($data['uf']) : null;

        Cliente::create([...$data, 'tenant_id' => $tenantId]);

        return back()->with('sucesso', 'Cliente cadastrado com sucesso!');
    }

    public function show(int $id)
    {
        $tenantId = session('tenant_id', 1);

        $cliente = Cliente::where('tenant_id', $tenantId)->findOrFail($id);

        $veiculos    = $cliente->veiculos()->get();
        $osDoCliente = $this->osService->byCliente($id);

        usort($osDoCliente, fn ($a, $b) => strcmp($b['data_entrada'], $a['data_entrada']));

        // Injeta a OS ativa em cada veículo pra exibir o badge de etapa.
        $osAtivasPorVeiculo = collect($osDoCliente)
            ->where('etapa_atual', '!=', 'finalizacao')
            ->keyBy('veiculo_id')
            ->all();

        $veiculos = $veiculos->map(function ($v) use ($osAtivasPorVeiculo) {
            $v->os_ativa = $osAtivasPorVeiculo[$v->id] ?? null;

            return $v;
        });

        $etapas = MockOsService::ETAPAS;

        return view('oficina.clientes.show', compact('cliente', 'veiculos', 'osDoCliente', 'etapas'));
    }
}
