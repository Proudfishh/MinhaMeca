<?php

namespace App\Services\Mock;

class MockClienteService
{
    public function all(int $tenantId = 1): array
    {
        return collect($this->data())->where('tenant_id', $tenantId)->values()->all();
    }

    public function find(int $id): ?array
    {
        return collect($this->data())->firstWhere('id', $id);
    }

    public function findByCpfEmail(string $cpf, string $email): ?array
    {
        $cliente = collect($this->data())->first(
            fn ($c) => $c['cpf'] === $cpf && $c['email'] === $email
        );

        if (! $cliente) return null;

        $oficinas = collect($this->data())
            ->where('cpf', $cpf)
            ->where('email', $email)
            ->map(fn ($c) => ['id' => $c['tenant_id'], 'nome' => 'Oficina #'.$c['tenant_id']])
            ->values()
            ->all();

        $cliente['oficinas'] = $oficinas;
        return $cliente;
    }

    private function data(): array
    {
        return [
            [
                'id'        => 1,
                'tenant_id' => 1,
                'nome'      => 'Carlos Henrique Souza',
                'cpf'       => '123.456.789-00',
                'email'     => 'carlos@email.com',
                'telefone'  => '(11) 99999-1111',
            ],
            [
                'id'        => 2,
                'tenant_id' => 1,
                'nome'      => 'Ana Paula Ferreira',
                'cpf'       => '234.567.890-11',
                'email'     => 'ana@email.com',
                'telefone'  => '(11) 99999-2222',
            ],
            [
                'id'        => 3,
                'tenant_id' => 1,
                'nome'      => 'Roberto Alves Lima',
                'cpf'       => '345.678.901-22',
                'email'     => 'roberto@email.com',
                'telefone'  => '(11) 99999-3333',
            ],
            [
                'id'        => 4,
                'tenant_id' => 2,
                'nome'      => 'Fernanda Costa',
                'cpf'       => '456.789.012-33',
                'email'     => 'fernanda@email.com',
                'telefone'  => '(19) 99999-4444',
            ],
        ];
    }
}
