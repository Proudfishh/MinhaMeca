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
