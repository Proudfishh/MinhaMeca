<?php

namespace App\Services\Mock;

class MockPendenciaService
{
    public function all(int $tenantId = 1): array
    {
        return collect($this->data())->where('tenant_id', $tenantId)->values()->all();
    }

    private function data(): array
    {
        return [
            [
                'id'           => 'PEND-2026-001',
                'tenant_id'    => 1,
                'tipo'         => 'os',
                'os_id'        => 'OS-2025-0047',
                'cliente_id'   => 1,
                'cliente'      => 'Carlos Henrique Souza',
                'descricao'    => 'Saldo restante OS-2025-0047',
                'valor_total'  => 900.00,
                'valor_pago'   => 300.00,
                'status'       => 'parcial',
                'data_criacao' => '2026-06-10',
                'parcelas'     => [
                    ['numero' => 1, 'valor' => 300.00, 'vencimento' => '2026-06-10', 'pago_em' => '2026-06-10', 'forma_pagamento' => 'Pix'],
                    ['numero' => 2, 'valor' => 300.00, 'vencimento' => '2026-06-25', 'pago_em' => null, 'forma_pagamento' => null],
                    ['numero' => 3, 'valor' => 300.00, 'vencimento' => '2026-07-25', 'pago_em' => null, 'forma_pagamento' => null],
                ],
            ],
            [
                'id'           => 'PEND-2026-002',
                'tenant_id'    => 1,
                'tipo'         => 'os',
                'os_id'        => 'OS-2025-0046',
                'cliente_id'   => 2,
                'cliente'      => 'Ana Paula Ferreira',
                'descricao'    => 'Saldo restante OS-2025-0046',
                'valor_total'  => 630.00,
                'valor_pago'   => 0.00,
                'status'       => 'vencido',
                'data_criacao' => '2026-06-05',
                'parcelas'     => [
                    ['numero' => 1, 'valor' => 630.00, 'vencimento' => '2026-06-10', 'pago_em' => null, 'forma_pagamento' => null],
                ],
            ],
            [
                'id'           => 'PEND-2026-003',
                'tenant_id'    => 1,
                'tipo'         => 'os',
                'os_id'        => 'OS-2025-0049',
                'cliente_id'   => 3,
                'cliente'      => 'Roberto Alves Lima',
                'descricao'    => 'Saldo restante OS-2025-0049',
                'valor_total'  => 400.00,
                'valor_pago'   => 400.00,
                'status'       => 'pago',
                'data_criacao' => '2026-06-01',
                'parcelas'     => [
                    ['numero' => 1, 'valor' => 200.00, 'vencimento' => '2026-06-05', 'pago_em' => '2026-06-05', 'forma_pagamento' => 'Dinheiro'],
                    ['numero' => 2, 'valor' => 200.00, 'vencimento' => '2026-06-12', 'pago_em' => '2026-06-12', 'forma_pagamento' => 'Pix'],
                ],
            ],
            [
                'id'           => 'PEND-2026-004',
                'tenant_id'    => 1,
                'tipo'         => 'avulso',
                'os_id'        => null,
                'cliente_id'   => 5,
                'cliente'      => 'Auto Peças Ltda',
                'descricao'    => 'Adiantamento para peças especiais',
                'valor_total'  => 500.00,
                'valor_pago'   => 0.00,
                'status'       => 'pendente',
                'data_criacao' => '2026-06-15',
                'parcelas'     => [
                    ['numero' => 1, 'valor' => 500.00, 'vencimento' => '2026-07-10', 'pago_em' => null, 'forma_pagamento' => null],
                ],
            ],
            [
                'id'           => 'PEND-2026-005',
                'tenant_id'    => 1,
                'tipo'         => 'avulso',
                'os_id'        => null,
                'cliente_id'   => 1,
                'cliente'      => 'Carlos Henrique Souza',
                'descricao'    => 'Acerto de serviços anteriores — prazo renegociado',
                'valor_total'  => 360.00,
                'valor_pago'   => 0.00,
                'status'       => 'negociado',
                'data_criacao' => '2026-06-01',
                'parcelas'     => [
                    ['numero' => 1, 'valor' => 180.00, 'vencimento' => '2026-07-15', 'pago_em' => null, 'forma_pagamento' => null],
                    ['numero' => 2, 'valor' => 180.00, 'vencimento' => '2026-08-15', 'pago_em' => null, 'forma_pagamento' => null],
                ],
            ],
        ];
    }
}
