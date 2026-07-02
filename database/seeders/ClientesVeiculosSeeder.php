<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Veiculo;
use Illuminate\Database\Seeder;

class ClientesVeiculosSeeder extends Seeder
{
    /**
     * IDs preservados de propósito — MockOsService e MockGarantiaService referenciam
     * estes mesmos cliente_id/veiculo_id, e continuam mockados nesta etapa.
     */
    public function run(): void
    {
        $clientes = [
            ['id' => 1, 'tenant_id' => 1, 'tipo' => 'pf', 'nome' => 'Carlos Henrique Souza', 'cpf' => '123.456.789-00', 'cnpj' => null, 'nome_contato' => null, 'email' => 'carlos@email.com', 'telefone' => '(11) 99999-1111', 'cep' => '01310-100', 'logradouro' => 'Av. Paulista', 'numero' => '1578', 'complemento' => 'Apto 42', 'bairro' => 'Bela Vista', 'cidade' => 'São Paulo', 'uf' => 'SP'],
            ['id' => 2, 'tenant_id' => 1, 'tipo' => 'pf', 'nome' => 'Ana Paula Ferreira', 'cpf' => '234.567.890-11', 'cnpj' => null, 'nome_contato' => null, 'email' => 'ana@email.com', 'telefone' => '(11) 99999-2222', 'cep' => null, 'logradouro' => null, 'numero' => null, 'complemento' => null, 'bairro' => null, 'cidade' => null, 'uf' => null],
            ['id' => 3, 'tenant_id' => 1, 'tipo' => 'pf', 'nome' => 'Roberto Alves Lima', 'cpf' => '345.678.901-22', 'cnpj' => null, 'nome_contato' => null, 'email' => 'roberto@email.com', 'telefone' => '(11) 99999-3333', 'cep' => null, 'logradouro' => null, 'numero' => null, 'complemento' => null, 'bairro' => null, 'cidade' => null, 'uf' => null],
            ['id' => 4, 'tenant_id' => 2, 'tipo' => 'pf', 'nome' => 'Fernanda Costa', 'cpf' => '456.789.012-33', 'cnpj' => null, 'nome_contato' => null, 'email' => 'fernanda@email.com', 'telefone' => '(19) 99999-4444', 'cep' => null, 'logradouro' => null, 'numero' => null, 'complemento' => null, 'bairro' => null, 'cidade' => null, 'uf' => null],
            ['id' => 5, 'tenant_id' => 1, 'tipo' => 'pj', 'nome' => 'Auto Peças Ltda', 'cpf' => null, 'cnpj' => '12.345.678/0001-99', 'nome_contato' => 'Fernando Braga', 'email' => 'contato@autopecas.com.br', 'telefone' => '(11) 3333-4444', 'cep' => '09750-000', 'logradouro' => 'Rua das Indústrias', 'numero' => '320', 'complemento' => 'Galpão B', 'bairro' => 'Distrito Industrial', 'cidade' => 'São Bernardo do Campo', 'uf' => 'SP'],
        ];

        foreach ($clientes as $c) {
            Cliente::updateOrCreate(['id' => $c['id']], $c);
        }

        $veiculos = [
            ['id' => 1, 'tenant_id' => 1, 'cliente_id' => 1, 'marca' => 'Honda', 'modelo' => 'Civic', 'ano' => 2019, 'cor' => 'Prata', 'placa' => 'ABC-1234', 'chassi' => '9BWZZZ377VT004251', 'combustivel' => 'Flex', 'cambio' => 'Automático', 'km' => 87400],
            ['id' => 2, 'tenant_id' => 1, 'cliente_id' => 2, 'marca' => 'Toyota', 'modelo' => 'Corolla', 'ano' => 2021, 'cor' => 'Branco', 'placa' => 'DEF-5678', 'chassi' => '9BWZZZ377VT007842', 'combustivel' => 'Flex', 'cambio' => 'Automático', 'km' => 42300],
            ['id' => 3, 'tenant_id' => 1, 'cliente_id' => 3, 'marca' => 'Volkswagen', 'modelo' => 'Polo', 'ano' => 2022, 'cor' => 'Preto', 'placa' => 'GHI-9012', 'chassi' => '9BWZZZ377VT009133', 'combustivel' => 'Flex', 'cambio' => 'Manual', 'km' => 28750],
            ['id' => 4, 'tenant_id' => 1, 'cliente_id' => 1, 'marca' => 'Chevrolet', 'modelo' => 'Onix', 'ano' => 2020, 'cor' => 'Vermelho', 'placa' => 'JKL-3456', 'chassi' => '9BWZZZ377VT006524', 'combustivel' => 'Flex', 'cambio' => 'Manual', 'km' => 61800],
        ];

        foreach ($veiculos as $v) {
            Veiculo::updateOrCreate(['id' => $v['id']], $v);
        }
    }
}
