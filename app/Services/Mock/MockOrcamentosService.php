<?php

namespace App\Services\Mock;

class MockOrcamentosService
{
    public function all(int $tenantId = 1): array
    {
        return collect($this->data())->where('tenant_id', $tenantId)->values()->all();
    }

    public function find(int $id): ?array
    {
        return collect($this->data())->firstWhere('id', $id);
    }

    private function data(): array
    {
        return [
            [
                'id'          => 1,
                'tenant_id'   => 1,
                'codigo'      => 'ORC-001',
                'cliente'     => 'João Silva',
                'veiculo'     => 'Honda Civic 2019',
                'placa'       => 'ABC-1234',
                'status'      => 'aprovado',
                'total'       => 340.00,
                'validade'    => '2026-07-05',
                'criado_em'   => '2026-06-19',
                'itens'       => [
                    ['descricao' => 'Pastilha de freio dianteira (par)', 'qtd' => 2, 'preco' => 85.00],
                    ['descricao' => 'Fluido de freio DOT4',              'qtd' => 1, 'preco' => 28.00],
                    ['descricao' => 'Mão de obra — troca de pastilhas',  'qtd' => 1, 'preco' => 142.00],
                ],
                'os_vinculada' => 'OS-2024-018',
            ],
            [
                'id'          => 2,
                'tenant_id'   => 1,
                'codigo'      => 'ORC-002',
                'cliente'     => 'Maria Oliveira',
                'veiculo'     => 'Toyota Corolla 2021',
                'placa'       => 'DEF-5678',
                'status'      => 'pendente',
                'total'       => 185.00,
                'validade'    => '2026-07-08',
                'criado_em'   => '2026-06-22',
                'itens'       => [
                    ['descricao' => 'Filtro de óleo universal',    'qtd' => 1, 'preco' => 22.00],
                    ['descricao' => 'Óleo motor 5W30 (4L)',        'qtd' => 1, 'preco' => 65.00],
                    ['descricao' => 'Mão de obra — troca de óleo', 'qtd' => 1, 'preco' => 98.00],
                ],
                'os_vinculada' => null,
            ],
            [
                'id'          => 3,
                'tenant_id'   => 1,
                'codigo'      => 'ORC-003',
                'cliente'     => null,
                'veiculo'     => null,
                'placa'       => null,
                'status'      => 'rascunho',
                'total'       => 520.00,
                'validade'    => '2026-07-10',
                'criado_em'   => '2026-06-23',
                'itens'       => [
                    ['descricao' => 'Vela de ignição NGK (4 un)', 'qtd' => 4,  'preco' => 18.00],
                    ['descricao' => 'Filtro de ar',                'qtd' => 1,  'preco' => 35.00],
                    ['descricao' => 'Diagnóstico eletrônico',      'qtd' => 1,  'preco' => 413.00],
                ],
                'os_vinculada' => null,
            ],
        ];
    }
}
