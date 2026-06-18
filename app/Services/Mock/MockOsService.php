<?php

namespace App\Services\Mock;

class MockOsService
{
    public const ETAPAS = [
        'checkin'       => ['label' => 'Check-in',        'cor' => '#94A3B8'],
        'diagnostico'   => ['label' => 'Diagnóstico',     'cor' => '#3B82F6'],
        'pecas'         => ['label' => 'Aguardando Peças', 'cor' => '#F59E0B'],
        'servico'       => ['label' => 'Serviço',         'cor' => '#7C3AED'],
        'testes'        => ['label' => 'Testes',          'cor' => '#06B6D4'],
        'finalizacao'   => ['label' => 'Finalização',     'cor' => '#10B981'],
    ];

    public function all(int $tenantId = 1): array
    {
        return collect($this->data())->where('tenant_id', $tenantId)->values()->all();
    }

    public function find(string $id): ?array
    {
        return collect($this->data())->firstWhere('id', $id);
    }

    public function byEtapa(int $tenantId = 1): array
    {
        $os = $this->all($tenantId);
        $agrupado = [];
        foreach (array_keys(self::ETAPAS) as $etapa) {
            $agrupado[$etapa] = collect($os)->where('etapa_atual', $etapa)->values()->all();
        }
        return $agrupado;
    }

    public function byCliente(int $clienteId): array
    {
        return collect($this->data())->where('cliente_id', $clienteId)->values()->all();
    }

    private function data(): array
    {
        return [
            [
                'id'                  => 'OS-2025-0035',
                'tenant_id'           => 1,
                'cliente_id'          => 1,
                'veiculo_id'          => 1,
                'cliente'             => 'Carlos Henrique Souza',
                'veiculo'             => 'Honda Civic 2019 · Prata · ABC-1234',
                'etapa_atual'         => 'finalizacao',
                'mecanico'            => 'Marcos Ferreira',
                'data_entrada'        => '2025-04-22',
                'previsao_entrega'    => '2025-04-25',
                'data_entrega_real'   => '2025-04-26',
                'descricao_cliente'   => 'Revisão geral e troca de óleo.',
                'servicos'            => [
                    ['descricao' => 'Revisão completa', 'valor' => 320.00, 'status' => 'concluido'],
                    ['descricao' => 'Troca de óleo',    'valor' => 95.00,  'status' => 'concluido'],
                    ['descricao' => 'Troca de filtros', 'valor' => 85.00,  'status' => 'concluido'],
                ],
                'total'               => 500.00,
                'etapa_checkin'       => ['checklist' => ['Lataria OK', 'Vidros OK', 'Pneus OK'], 'fotos' => [], 'observacao' => ''],
                'etapa_diagnostico'   => ['descricao' => 'Revisão de rotina. Sem anomalias.'],
                'etapa_pecas'         => ['aprovado' => true, 'aprovado_em' => '2025-04-22 14:00', 'itens' => [
                    ['descricao' => 'Óleo motor 5W30 (4L)', 'origem' => 'estoque', 'qtd' => 1, 'valor' => 65.00],
                    ['descricao' => 'Kit filtros Civic',    'origem' => 'estoque', 'qtd' => 1, 'valor' => 75.00],
                ]],
                'etapa_servico'       => ['logs' => [
                    ['hora' => '2025-04-23 08:00', 'descricao' => 'Revisão iniciada.'],
                    ['hora' => '2025-04-23 16:00', 'descricao' => 'Todos os serviços concluídos.'],
                ]],
                'etapa_finalizacao'   => [
                    'checklist_saida' => ['Motor OK', 'Óleo OK', 'Filtros OK'],
                    'observacoes'     => 'Veículo entregue em perfeitas condições.',
                    'fotos'           => [],
                ],
                'historico_transicoes' => [
                    ['de' => 'checkin',     'para' => 'diagnostico', 'em' => '2025-04-22 09:00', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'diagnostico', 'para' => 'pecas',       'em' => '2025-04-22 11:00', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'pecas',       'para' => 'servico',     'em' => '2025-04-23 08:00', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'servico',     'para' => 'testes',      'em' => '2025-04-23 16:00', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'testes',      'para' => 'finalizacao', 'em' => '2025-04-24 10:00', 'responsavel' => 'Marcos Ferreira'],
                ],
            ],
            [
                'id'                  => 'OS-2025-0047',
                'tenant_id'           => 1,
                'cliente_id'          => 1,
                'veiculo_id'          => 1,
                'cliente'             => 'Carlos Henrique Souza',
                'veiculo'             => 'Honda Civic 2019 · Prata · ABC-1234',
                'etapa_atual'         => 'servico',
                'mecanico'            => 'Marcos Ferreira',
                'data_entrada'        => '2025-06-10',
                'previsao_entrega'    => '2025-06-16',
                'descricao_cliente'   => 'Barulho ao frear e carro puxando para o lado.',
                'servicos'            => [
                    ['descricao' => 'Troca de correia dentada',      'valor' => 280.00, 'status' => 'em_andamento'],
                    ['descricao' => 'Alinhamento e balanceamento',   'valor' => 120.00, 'status' => 'pendente'],
                    ['descricao' => 'Troca de óleo',                 'valor' => 95.00,  'status' => 'concluido'],
                ],
                'total'               => 495.00,
                'etapa_checkin'       => [
                    'checklist'  => ['Lataria OK', 'Vidros OK', 'Pneus OK', 'Documentos verificados'],
                    'fotos'      => [],
                    'observacao' => 'Pequeno arranhão no para-choque traseiro já existente.',
                ],
                'etapa_diagnostico'   => [
                    'descricao' => 'Desgaste severo nas pastilhas de freio dianteiras. Correia dentada com trincos visíveis — risco de ruptura. Óleo do motor vencido.',
                ],
                'etapa_pecas'         => [
                    'aprovado'    => true,
                    'aprovado_em' => '2025-06-11 10:30',
                    'itens'       => [
                        ['descricao' => 'Pastilha de freio dianteira', 'origem' => 'estoque',  'qtd' => 1, 'valor' => 85.00],
                        ['descricao' => 'Correia dentada Honda Civic', 'origem' => 'externo',  'qtd' => 1, 'valor' => 145.00],
                        ['descricao' => 'Óleo motor 5W30 (4L)',        'origem' => 'estoque',  'qtd' => 1, 'valor' => 65.00],
                    ],
                ],
                'etapa_servico'       => [
                    'logs' => [
                        ['hora' => '2025-06-12 09:00', 'descricao' => 'Início da troca de correia dentada.'],
                        ['hora' => '2025-06-12 11:45', 'descricao' => 'Correia trocada. Iniciando troca de óleo.'],
                        ['hora' => '2025-06-12 12:30', 'descricao' => 'Óleo trocado. Aguardando peças para freio.'],
                    ],
                ],
                'historico_transicoes' => [
                    ['de' => 'checkin',     'para' => 'diagnostico', 'em' => '2025-06-10 14:00', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'diagnostico', 'para' => 'pecas',       'em' => '2025-06-10 16:30', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'pecas',       'para' => 'servico',     'em' => '2025-06-12 09:00', 'responsavel' => 'Marcos Ferreira'],
                ],
            ],
            [
                'id'                  => 'OS-2025-0048',
                'tenant_id'           => 1,
                'cliente_id'          => 2,
                'veiculo_id'          => 2,
                'cliente'             => 'Ana Paula Ferreira',
                'veiculo'             => 'Toyota Corolla 2021 · Branco · DEF-5678',
                'etapa_atual'         => 'pecas',
                'mecanico'            => 'João Oliveira',
                'data_entrada'        => '2025-06-13',
                'previsao_entrega'    => '2025-06-18',
                'descricao_cliente'   => 'Luz do motor acesa e consumo de combustível alto.',
                'servicos'            => [
                    ['descricao' => 'Diagnóstico eletrônico',  'valor' => 150.00, 'status' => 'concluido'],
                    ['descricao' => 'Troca de injetores',      'valor' => 480.00, 'status' => 'pendente'],
                ],
                'total'               => 630.00,
                'etapa_checkin'       => ['checklist' => ['Lataria OK', 'Vidros OK', 'Pneus OK'], 'fotos' => [], 'observacao' => ''],
                'etapa_diagnostico'   => ['descricao' => 'Injetores entupidos causando mistura rica. Código P0300 identificado.'],
                'etapa_pecas'         => [
                    'aprovado'    => false,
                    'aprovado_em' => null,
                    'itens'       => [
                        ['descricao' => 'Jogo de injetores Corolla', 'origem' => 'externo', 'qtd' => 4, 'valor' => 320.00],
                    ],
                ],
                'etapa_servico'       => ['logs' => []],
                'historico_transicoes' => [
                    ['de' => 'checkin',     'para' => 'diagnostico', 'em' => '2025-06-13 10:00', 'responsavel' => 'João Oliveira'],
                    ['de' => 'diagnostico', 'para' => 'pecas',       'em' => '2025-06-13 15:00', 'responsavel' => 'João Oliveira'],
                ],
            ],
            [
                'id'                  => 'OS-2025-0049',
                'tenant_id'           => 1,
                'cliente_id'          => 3,
                'veiculo_id'          => 3,
                'cliente'             => 'Roberto Alves Lima',
                'veiculo'             => 'Volkswagen Polo 2022 · Preto · GHI-9012',
                'etapa_atual'         => 'testes',
                'mecanico'            => 'Marcos Ferreira',
                'data_entrada'        => '2025-06-09',
                'previsao_entrega'    => '2025-06-14',
                'descricao_cliente'   => 'Revisão de 30.000 km.',
                'servicos'            => [
                    ['descricao' => 'Revisão completa 30k',     'valor' => 350.00, 'status' => 'concluido'],
                    ['descricao' => 'Troca de filtros',         'valor' => 95.00,  'status' => 'concluido'],
                    ['descricao' => 'Verificação de freios',    'valor' => 80.00,  'status' => 'concluido'],
                ],
                'total'               => 525.00,
                'etapa_checkin'       => ['checklist' => ['Lataria OK', 'Vidros OK', 'Pneus OK'], 'fotos' => [], 'observacao' => ''],
                'etapa_diagnostico'   => ['descricao' => 'Revisão de rotina. Sem anomalias críticas detectadas.'],
                'etapa_pecas'         => ['aprovado' => true, 'aprovado_em' => '2025-06-09 14:00', 'itens' => [
                    ['descricao' => 'Kit filtros Polo',  'origem' => 'estoque', 'qtd' => 1, 'valor' => 75.00],
                    ['descricao' => 'Óleo 5W30 (4L)',    'origem' => 'estoque', 'qtd' => 1, 'valor' => 65.00],
                ]],
                'etapa_servico'       => ['logs' => [
                    ['hora' => '2025-06-10 08:30', 'descricao' => 'Revisão iniciada.'],
                    ['hora' => '2025-06-10 17:00', 'descricao' => 'Serviços concluídos. Enviando para testes.'],
                ]],
                'historico_transicoes' => [
                    ['de' => 'checkin',     'para' => 'diagnostico', 'em' => '2025-06-09 09:00', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'diagnostico', 'para' => 'pecas',       'em' => '2025-06-09 11:00', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'pecas',       'para' => 'servico',     'em' => '2025-06-10 08:30', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'servico',     'para' => 'testes',      'em' => '2025-06-10 17:00', 'responsavel' => 'Marcos Ferreira'],
                ],
            ],
            [
                'id'                  => 'OS-2025-0050',
                'tenant_id'           => 1,
                'cliente_id'          => 1,
                'veiculo_id'          => 4,
                'cliente'             => 'Carlos Henrique Souza',
                'veiculo'             => 'Chevrolet Onix 2020 · Vermelho · JKL-3456',
                'etapa_atual'         => 'checkin',
                'mecanico'            => 'João Oliveira',
                'data_entrada'        => '2025-06-14',
                'previsao_entrega'    => null,
                'descricao_cliente'   => 'Ar-condicionado não gela e barulho no motor.',
                'servicos'            => [],
                'total'               => 0.00,
                'etapa_checkin'       => ['checklist' => [], 'fotos' => [], 'observacao' => ''],
                'etapa_diagnostico'   => ['descricao' => ''],
                'etapa_pecas'         => ['aprovado' => false, 'aprovado_em' => null, 'itens' => []],
                'etapa_servico'       => ['logs' => []],
                'historico_transicoes' => [],
            ],
            [
                'id'                  => 'OS-2025-0046',
                'tenant_id'           => 1,
                'cliente_id'          => 2,
                'veiculo_id'          => 2,
                'cliente'             => 'Ana Paula Ferreira',
                'veiculo'             => 'Toyota Corolla 2021 · Branco · DEF-5678',
                'etapa_atual'         => 'finalizacao',
                'mecanico'            => 'Marcos Ferreira',
                'data_entrada'        => '2025-06-05',
                'previsao_entrega'    => '2025-06-13',
                'descricao_cliente'   => 'Troca de amortecedores.',
                'servicos'            => [
                    ['descricao' => 'Troca amortecedores dianteiros', 'valor' => 620.00, 'status' => 'concluido'],
                    ['descricao' => 'Alinhamento',                    'valor' => 120.00, 'status' => 'concluido'],
                ],
                'total'               => 740.00,
                'etapa_checkin'       => ['checklist' => ['Lataria OK', 'Vidros OK', 'Pneus OK'], 'fotos' => [], 'observacao' => ''],
                'etapa_diagnostico'   => ['descricao' => 'Amortecedores dianteiros com vazamento. Suspensão comprometida.'],
                'etapa_pecas'         => ['aprovado' => true, 'aprovado_em' => '2025-06-06 09:00', 'itens' => [
                    ['descricao' => 'Par amortecedores dianteiros Corolla', 'origem' => 'externo', 'qtd' => 1, 'valor' => 480.00],
                ]],
                'etapa_servico'       => ['logs' => [
                    ['hora' => '2025-06-08 08:00', 'descricao' => 'Troca de amortecedores concluída.'],
                    ['hora' => '2025-06-08 10:30', 'descricao' => 'Alinhamento realizado.'],
                ]],
                'etapa_finalizacao'   => [
                    'checklist_saida' => ['Teste de frenagem OK', 'Suspensão OK', 'Documentação entregue'],
                    'observacoes'     => 'Veículo em perfeitas condições. Cliente orientado sobre revisão em 10.000 km.',
                    'fotos'           => [],
                ],
                'historico_transicoes' => [
                    ['de' => 'checkin',     'para' => 'diagnostico', 'em' => '2025-06-05 11:00', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'diagnostico', 'para' => 'pecas',       'em' => '2025-06-05 14:00', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'pecas',       'para' => 'servico',     'em' => '2025-06-08 08:00', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'servico',     'para' => 'testes',      'em' => '2025-06-08 11:00', 'responsavel' => 'Marcos Ferreira'],
                    ['de' => 'testes',      'para' => 'finalizacao', 'em' => '2025-06-08 12:00', 'responsavel' => 'Marcos Ferreira'],
                ],
            ],
        ];
    }
}
