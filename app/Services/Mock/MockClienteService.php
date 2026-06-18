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
            fn ($c) => ($c['cpf'] ?? '') === $cpf && $c['email'] === $email
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
                'id'            => 1,
                'tenant_id'     => 1,
                'tipo'          => 'pf',
                'nome'          => 'Carlos Henrique Souza',
                'cpf'           => '123.456.789-00',
                'cnpj'          => null,
                'nome_contato'  => null,
                'email'         => 'carlos@email.com',
                'telefone'      => '(11) 99999-1111',
                'cep'           => '01310-100',
                'logradouro'    => 'Av. Paulista',
                'numero'        => '1578',
                'complemento'   => 'Apto 42',
                'bairro'        => 'Bela Vista',
                'cidade'        => 'São Paulo',
                'uf'            => 'SP',
            ],
            [
                'id'            => 2,
                'tenant_id'     => 1,
                'tipo'          => 'pf',
                'nome'          => 'Ana Paula Ferreira',
                'cpf'           => '234.567.890-11',
                'cnpj'          => null,
                'nome_contato'  => null,
                'email'         => 'ana@email.com',
                'telefone'      => '(11) 99999-2222',
                'cep'           => null,
                'logradouro'    => null,
                'numero'        => null,
                'complemento'   => null,
                'bairro'        => null,
                'cidade'        => null,
                'uf'            => null,
            ],
            [
                'id'            => 3,
                'tenant_id'     => 1,
                'tipo'          => 'pf',
                'nome'          => 'Roberto Alves Lima',
                'cpf'           => '345.678.901-22',
                'cnpj'          => null,
                'nome_contato'  => null,
                'email'         => 'roberto@email.com',
                'telefone'      => '(11) 99999-3333',
                'cep'           => null,
                'logradouro'    => null,
                'numero'        => null,
                'complemento'   => null,
                'bairro'        => null,
                'cidade'        => null,
                'uf'            => null,
            ],
            [
                'id'            => 4,
                'tenant_id'     => 2,
                'tipo'          => 'pf',
                'nome'          => 'Fernanda Costa',
                'cpf'           => '456.789.012-33',
                'cnpj'          => null,
                'nome_contato'  => null,
                'email'         => 'fernanda@email.com',
                'telefone'      => '(19) 99999-4444',
                'cep'           => null,
                'logradouro'    => null,
                'numero'        => null,
                'complemento'   => null,
                'bairro'        => null,
                'cidade'        => null,
                'uf'            => null,
            ],
            [
                'id'            => 5,
                'tenant_id'     => 1,
                'tipo'          => 'pj',
                'nome'          => 'Auto Peças Ltda',
                'cpf'           => null,
                'cnpj'          => '12.345.678/0001-99',
                'nome_contato'  => 'Fernando Braga',
                'email'         => 'contato@autopecas.com.br',
                'telefone'      => '(11) 3333-4444',
                'cep'           => '09750-000',
                'logradouro'    => 'Rua das Indústrias',
                'numero'        => '320',
                'complemento'   => 'Galpão B',
                'bairro'        => 'Distrito Industrial',
                'cidade'        => 'São Bernardo do Campo',
                'uf'            => 'SP',
            ],
        ];
    }
}
