<?php

namespace App\Services\Mock;

class MockGarantiaService
{
    public function all(int $tenantId = 1): array
    {
        return collect($this->data())->where('tenant_id', $tenantId)->values()->all();
    }

    private function data(): array
    {
        return [
            [
                'id'               => 'GARAN-2026-001',
                'tenant_id'        => 1,
                'os_id'            => 'OS-2025-0047',
                'cliente_id'       => 1,
                'cliente'          => 'Carlos Henrique Souza',
                'veiculo_id'       => 1,
                'veiculo'          => 'Honda Civic 2019 · Prata · ABC-1234',
                'data_entrega'     => '2026-05-20',
                'data_vencimento'  => '2026-08-17',
                'status'           => 'ativa',
                'os_retrabalho_id' => null,
            ],
            [
                'id'               => 'GARAN-2026-002',
                'tenant_id'        => 1,
                'os_id'            => 'OS-2025-0048',
                'cliente_id'       => 2,
                'cliente'          => 'Ana Paula Ferreira',
                'veiculo_id'       => 2,
                'veiculo'          => 'Toyota Corolla 2021 · Branco · DEF-5678',
                'data_entrega'     => '2026-05-25',
                'data_vencimento'  => '2026-08-23',
                'status'           => 'ativa',
                'os_retrabalho_id' => null,
            ],
            [
                'id'               => 'GARAN-2026-003',
                'tenant_id'        => 1,
                'os_id'            => 'OS-2025-0049',
                'cliente_id'       => 3,
                'cliente'          => 'Roberto Alves Lima',
                'veiculo_id'       => 3,
                'veiculo'          => 'Volkswagen Polo 2022 · Preto · GHI-9012',
                'data_entrega'     => '2026-06-07',
                'data_vencimento'  => '2026-06-22',
                'status'           => 'vencendo',
                'os_retrabalho_id' => null,
            ],
            [
                'id'               => 'GARAN-2026-004',
                'tenant_id'        => 1,
                'os_id'            => 'OS-2025-0050',
                'cliente_id'       => 4,
                'cliente'          => 'Fernanda Costa',
                'veiculo_id'       => 4,
                'veiculo'          => 'Fiat Palio 2016 · Vermelho · JKL-3456',
                'data_entrega'     => '2026-03-01',
                'data_vencimento'  => '2026-05-30',
                'status'           => 'expirada',
                'os_retrabalho_id' => null,
            ],
            [
                'id'               => 'GARAN-2026-005',
                'tenant_id'        => 1,
                'os_id'            => 'OS-2025-0051',
                'cliente_id'       => 5,
                'cliente'          => 'Auto Peças Ltda',
                'veiculo_id'       => 5,
                'veiculo'          => 'Fiat Strada 2020 · Branco · MNO-7890',
                'data_entrega'     => '2026-05-10',
                'data_vencimento'  => '2026-08-07',
                'status'           => 'acionada',
                'os_retrabalho_id' => 'OS-RET-2026-001',
            ],
        ];
    }
}
