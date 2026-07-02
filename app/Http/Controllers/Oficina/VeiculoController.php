<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Veiculo;
use App\Services\Mock\MockOsService;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{
    public function __construct(private MockOsService $osService) {}

    public function index()
    {
        $tenantId = session('tenant_id', 1);

        $veiculos = Veiculo::where('tenant_id', $tenantId)
            ->with('cliente')
            ->get()
            ->map(function (Veiculo $v) {
                $v->cliente_nome = $v->cliente->nome ?? '—';
                $osDoVeiculo     = collect($this->osService->all(session('tenant_id', 1)))
                    ->where('veiculo_id', $v->id);
                $v->total_os = $osDoVeiculo->count();
                $v->os_ativa = $osDoVeiculo->first(fn ($os) => empty($os['data_entrega_real']));

                return $v;
            });

        $etapas = MockOsService::ETAPAS;

        $clientes = Cliente::where('tenant_id', $tenantId)->orderBy('nome')->get(['id', 'nome']);

        return view('oficina.veiculos.index', compact('veiculos', 'etapas', 'clientes'));
    }

    public function store(Request $request)
    {
        $tenantId = session('tenant_id', 1);

        $data = $request->validate([
            'marca'       => ['required', 'string', 'max:255'],
            'modelo'      => ['required', 'string', 'max:255'],
            'placa'       => ['required', 'string', 'max:20'],
            'ano'         => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'cor'         => ['nullable', 'string', 'max:255'],
            'cliente_id'  => ['nullable', 'integer', 'exists:clientes,id'],
            'chassi'      => ['nullable', 'string', 'max:255'],
            'km'          => ['nullable', 'integer', 'min:0'],
            'combustivel' => ['nullable', 'string', 'max:255'],
            'cambio'      => ['nullable', 'string', 'max:255'],
        ]);

        $data['placa'] = strtoupper($data['placa']);
        $data['km']    = $data['km'] ?? 0;

        Veiculo::create([...$data, 'tenant_id' => $tenantId]);

        return back()->with('sucesso', 'Veículo cadastrado com sucesso!');
    }

    public function show(int $id)
    {
        $tenantId = session('tenant_id', 1);

        $veiculo = Veiculo::where('tenant_id', $tenantId)->with('cliente')->findOrFail($id);
        $cliente = $veiculo->cliente;

        $osDoVeiculo = collect($this->osService->all($tenantId))
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
