<?php

namespace App\Services\Mock;

class MockConfiguracaoService
{
    public function get(int $tenantId = 1): array
    {
        return [
            'oficina' => [
                'nome'       => 'Auto Center Premium',
                'cnpj'       => '12.345.678/0001-99',
                'logradouro' => 'Av. Paulista',
                'numero'     => '1578',
                'bairro'     => 'Bela Vista',
                'cidade'     => 'São Paulo',
                'uf'         => 'SP',
                'cep'        => '01310-100',
                'telefone'   => '(11) 3333-4444',
                'email'      => 'contato@autocenterpremium.com.br',
                'site'       => 'www.autocenterpremium.com.br',
            ],
            'horario' => [
                ['dia' => 'Segunda-feira',  'ativo' => true,  'abertura' => '08:00', 'fechamento' => '18:00'],
                ['dia' => 'Terça-feira',   'ativo' => true,  'abertura' => '08:00', 'fechamento' => '18:00'],
                ['dia' => 'Quarta-feira',  'ativo' => true,  'abertura' => '08:00', 'fechamento' => '18:00'],
                ['dia' => 'Quinta-feira',  'ativo' => true,  'abertura' => '08:00', 'fechamento' => '18:00'],
                ['dia' => 'Sexta-feira',   'ativo' => true,  'abertura' => '08:00', 'fechamento' => '18:00'],
                ['dia' => 'Sábado',        'ativo' => true,  'abertura' => '08:00', 'fechamento' => '13:00'],
                ['dia' => 'Domingo',       'ativo' => false, 'abertura' => '08:00', 'fechamento' => '12:00'],
            ],
            'os_config' => [
                'prazo_garantia' => 90,
                'prefixo_os'     => 'OS-2026-',
            ],
            'portal' => [
                'mensagem_boas_vindas'       => 'Olá! Acompanhe aqui o andamento do seu veículo em tempo real.',
                'exibir_previsao_entrega'    => true,
                'exibir_lista_servicos'      => true,
            ],
            'conta' => [
                'nome'     => 'Gabriel Peixoto',
                'email'    => 'gabriel@autocenterpremium.com.br',
                'telefone' => '(11) 99999-0000',
                'notificacoes' => [
                    'os_concluida'        => true,
                    'garantia_vencendo'   => true,
                    'estoque_baixo'       => false,
                    'pendencia_vencida'   => true,
                    'novo_funcionario'    => true,
                ],
            ],
            'equipe' => [
                'pendentes' => [
                    [
                        'id'    => 101,
                        'nome'  => 'Lucas Martins',
                        'email' => 'lucas@email.com',
                        'data'  => '2026-06-17',
                    ],
                ],
                'membros' => [
                    ['id' => 1,  'nome' => 'Gabriel Peixoto',  'email' => 'gabriel@autocenterpremium.com.br', 'papel' => 'dono',       'status' => 'ativo'],
                    ['id' => 2,  'nome' => 'Marcos Ferreira',  'email' => 'marcos@autocenterpremium.com.br',  'papel' => 'mecanico',   'status' => 'ativo'],
                    ['id' => 3,  'nome' => 'João Oliveira',    'email' => 'joao@autocenterpremium.com.br',    'papel' => 'mecanico',   'status' => 'ativo'],
                    ['id' => 4,  'nome' => 'Carla Souza',      'email' => 'carla@autocenterpremium.com.br',   'papel' => 'recepcao',   'status' => 'ativo'],
                    ['id' => 5,  'nome' => 'Paulo Henrique',   'email' => 'paulo@autocenterpremium.com.br',   'papel' => 'financeiro', 'status' => 'ativo'],
                ],
            ],
            'assinatura' => [
                'plano'      => 'Profissional',
                'status'     => 'ativo',
                'renovacao'  => '2026-07-18',
                'valor'      => 149.90,
                'recursos'   => [
                    'Usuários ilimitados',
                    'OSes ilimitadas',
                    'Portal do cliente',
                    'Módulo de garantias',
                    'Controle de estoque',
                    'Relatórios básicos',
                ],
                'cartao' => [
                    'ultimos4'  => '4242',
                    'validade'  => '12/27',
                    'bandeira'  => 'Visa',
                ],
                'faturas' => [
                    ['data' => '2026-06-18', 'periodo' => 'Jun/2026', 'valor' => 149.90, 'status' => 'Pago'],
                    ['data' => '2026-05-18', 'periodo' => 'Mai/2026', 'valor' => 149.90, 'status' => 'Pago'],
                    ['data' => '2026-04-18', 'periodo' => 'Abr/2026', 'valor' => 149.90, 'status' => 'Pago'],
                ],
            ],
        ];
    }
}
