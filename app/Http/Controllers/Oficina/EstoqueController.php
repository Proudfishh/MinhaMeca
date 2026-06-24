<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Services\Mock\MockEstoqueService;

class EstoqueController extends Controller
{
    public function __construct(private MockEstoqueService $estoqueService) {}

    public function index()
    {
        $tenantId = session('tenant_id', 1);
        $itens    = $this->estoqueService->all($tenantId);

        foreach ($itens as &$item) {
            $item['status'] = $item['quantidade'] <= 0
                ? 'sem_estoque'
                : ($item['quantidade'] <= $item['estoque_minimo'] ? 'baixo' : 'ok');
        }
        unset($item);

        $metricas = [
            'total'       => count($itens),
            'baixo'       => collect($itens)->where('status', 'baixo')->count(),
            'sem_estoque' => collect($itens)->where('status', 'sem_estoque')->count(),
            'valor_total' => collect($itens)->sum(fn ($i) => $i['quantidade'] * $i['valor_unitario']),
        ];

        $categorias = collect($itens)
            ->pluck('categoria')
            ->filter()
            ->unique()
            ->values()
            ->all();

        return view('oficina.estoque.index', compact('itens', 'metricas', 'categorias'));
    }
}
