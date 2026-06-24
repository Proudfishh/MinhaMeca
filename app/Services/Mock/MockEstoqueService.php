<?php

namespace App\Services\Mock;

class MockEstoqueService
{
    public function all(int $tenantId = 1): array
    {
        return collect($this->data())->where('tenant_id', $tenantId)->values()->all();
    }

    private function data(): array
    {
        return [
            ['id' => 1, 'tenant_id' => 1, 'descricao' => 'Pastilha de freio dianteira (par)', 'quantidade' => 4,  'valor_unitario' => 85.00,  'custo' => 52.00, 'estoque_minimo' => 2, 'categoria' => 'Freios',   'localizacao' => 'Prateleira A1'],
            ['id' => 2, 'tenant_id' => 1, 'descricao' => 'Óleo motor 5W30 (4L)',              'quantidade' => 8,  'valor_unitario' => 65.00,  'custo' => 38.00, 'estoque_minimo' => 3, 'categoria' => 'Óleos',    'localizacao' => 'Prateleira B2'],
            ['id' => 3, 'tenant_id' => 1, 'descricao' => 'Filtro de óleo universal',          'quantidade' => 10, 'valor_unitario' => 22.00,  'custo' => 12.00, 'estoque_minimo' => 4, 'categoria' => 'Filtros',  'localizacao' => 'Prateleira B1'],
            ['id' => 4, 'tenant_id' => 1, 'descricao' => 'Filtro de ar',                      'quantidade' => 6,  'valor_unitario' => 35.00,  'custo' => 20.00, 'estoque_minimo' => 3, 'categoria' => 'Filtros',  'localizacao' => 'Prateleira B1'],
            ['id' => 5, 'tenant_id' => 1, 'descricao' => 'Vela de ignição NGK (un)',          'quantidade' => 1,  'valor_unitario' => 18.00,  'custo' => 10.00, 'estoque_minimo' => 4, 'categoria' => 'Ignição',  'localizacao' => 'Gaveta C3'],
            ['id' => 6, 'tenant_id' => 1, 'descricao' => 'Fluido de freio DOT4 (500ml)',      'quantidade' => 5,  'valor_unitario' => 28.00,  'custo' => 16.00, 'estoque_minimo' => 2, 'categoria' => 'Freios',   'localizacao' => 'Prateleira A2'],
            ['id' => 7, 'tenant_id' => 1, 'descricao' => 'Correia poly-V universal',          'quantidade' => 0,  'valor_unitario' => 95.00,  'custo' => 58.00, 'estoque_minimo' => 2, 'categoria' => 'Correias', 'localizacao' => 'Prateleira D1'],
        ];
    }
}
