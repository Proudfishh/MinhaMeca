<?php

namespace App\Services\Mock;

class MockVeiculoService
{
    public function all(int $tenantId = 1): array
    {
        return collect($this->data())->where('tenant_id', $tenantId)->values()->all();
    }

    public function find(int $id): ?array
    {
        return collect($this->data())->firstWhere('id', $id);
    }

    public function byCliente(int $clienteId): array
    {
        return collect($this->data())->where('cliente_id', $clienteId)->values()->all();
    }

    private function data(): array
    {
        return [
            [
                'id'          => 1,
                'tenant_id'   => 1,
                'cliente_id'  => 1,
                'marca'       => 'Honda',
                'modelo'      => 'Civic',
                'ano'         => 2019,
                'cor'         => 'Prata',
                'placa'       => 'ABC-1234',
                'chassi'      => '9BWZZZ377VT004251',
                'combustivel' => 'Flex',
                'cambio'      => 'Automático',
                'km'          => 87400,
                'imagem'      => null,
            ],
            [
                'id'          => 2,
                'tenant_id'   => 1,
                'cliente_id'  => 2,
                'marca'       => 'Toyota',
                'modelo'      => 'Corolla',
                'ano'         => 2021,
                'cor'         => 'Branco',
                'placa'       => 'DEF-5678',
                'chassi'      => '9BWZZZ377VT007842',
                'combustivel' => 'Flex',
                'cambio'      => 'Automático',
                'km'          => 42300,
                'imagem'      => null,
            ],
            [
                'id'          => 3,
                'tenant_id'   => 1,
                'cliente_id'  => 3,
                'marca'       => 'Volkswagen',
                'modelo'      => 'Polo',
                'ano'         => 2022,
                'cor'         => 'Preto',
                'placa'       => 'GHI-9012',
                'chassi'      => '9BWZZZ377VT009133',
                'combustivel' => 'Flex',
                'cambio'      => 'Manual',
                'km'          => 28750,
                'imagem'      => null,
            ],
            [
                'id'          => 4,
                'tenant_id'   => 1,
                'cliente_id'  => 1,
                'marca'       => 'Chevrolet',
                'modelo'      => 'Onix',
                'ano'         => 2020,
                'cor'         => 'Vermelho',
                'placa'       => 'JKL-3456',
                'chassi'      => '9BWZZZ377VT006524',
                'combustivel' => 'Flex',
                'cambio'      => 'Manual',
                'km'          => 61800,
                'imagem'      => null,
            ],
        ];
    }
}
