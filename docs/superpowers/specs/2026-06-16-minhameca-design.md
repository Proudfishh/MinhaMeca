# MinhaMeca — Design Spec — Fase 1

**Data:** 2026-06-16
**Stack:** Laravel 11 · PHP 8.2 · Blade · Livewire · Alpine.js · Tailwind CSS
**Fase:** 1 — Frontend com dados mockados (sem lógica de backend real)

---

## 1. Visão do Produto

MinhaMeca é uma plataforma SaaS multi-tenant para gestão de oficinas mecânicas. Uma única instância hospeda múltiplas oficinas como clientes independentes, com dados completamente isolados por tenant.

O sistema possui dois portais com experiências radicalmente diferentes:
- **Portal da Oficina:** SaaS denso em informação para o dono/funcionário gerenciar a operação
- **Portal do Cliente:** App consumer simples e emocional para o cliente acompanhar o carro

---

## 2. Arquitetura

### Separação de portais
Domínio único com prefixos de rota:
- `/oficina/*` — Portal da Oficina (Guard: `oficina`)
- `/cliente/*` — Portal do Cliente (Guard: `cliente`)
- `/login` — Página única com toggle entre os dois portais

### Multi-tenancy
Estratégia: `tenant_id` nas tabelas (Fase 2). Na Fase 1, um `TenantMiddleware` injeta um objeto `Tenant` mockado na requisição. A interface do middleware é idêntica ao que será usado em produção — apenas a fonte dos dados muda.

### Autenticação
Dois Laravel Guards independentes (`oficina` e `cliente`). Um usuário autenticado como oficina não acessa rotas de cliente e vice-versa.

**Fluxo de login do cliente:** identificação por CPF + email. Se o par existir em apenas uma oficina, acesso direto. Se existir em múltiplas, apresenta lista de seleção de oficina.

**Aprovação de cliente na etapa "Aguardando Peças":** registrada internamente pelo atendente da oficina (ex: "cliente aprovou por telefone") — não requer ação no portal do cliente.

### Dados mockados
Camada `app/Services/Mock/` com classes que retornam arrays PHP estruturados. Controllers e Livewire components injetam via construtor — na Fase 2, a implementação da service é trocada sem tocar em views ou components.

**Dados mínimos para Fase 1:**
- 3 tenants (oficinas) com dados isolados
- 5 OS com status diferentes cobrindo todas as etapas
- Clientes, veículos, peças e histórico de transições coerentes entre si

---

## 3. Fluxo da Ordem de Serviço

### Etapas

| Etapa | Cor | Conteúdo |
|---|---|---|
| Check-in | Cinza `#94A3B8` | Cadastro/link de cliente+veículo, checklist de entrada, fotos internas/externas |
| Diagnóstico | Azul `#3B82F6` | Descrição detalhada do problema identificado |
| Aguardando Peças | Âmbar `#F59E0B` | Aprovação interna do cliente, orçamento de peças, origem (estoque/externo), status de recebimento, valores para financeiro |
| Serviço | Violeta `#7C3AED` | Execução — com ou sem logs de progresso detalhados |
| Testes | Ciano `#06B6D4` | Validação do serviço executado |
| Finalização | Verde `#10B981` | Checklist de saída, fotos e informações finais |

### Regras de transição
- **Livre** — qualquer etapa pode avançar ou regredir para qualquer outra
- **Histórico obrigatório** — toda transição gera registro: `etapa_origem → etapa_destino + timestamp + responsável`
- Exemplo real: `Check-in → Diagnóstico → Aguardando Peças → Serviço → Testes → Diagnóstico → Aguardando Peças → Serviço → Finalização`

### Visibilidade para o cliente
O cliente vê no portal dele:
- Nome da etapa atual + descrição amigável (ex: "Seu carro está sendo diagnosticado")
- Histórico simplificado das etapas percorridas
- Orçamento aprovado (visível somente após aprovação interna)

O cliente **não vê:** notas internas do mecânico, orçamentos não aprovados, dados de fornecedores.

---

## 4. Mapa de Telas

### Portal da Oficina

```
/login
└── Toggle Oficina / Cliente
    └── Aba Oficina: Login | Criar Conta | Esqueci Senha

/oficina/dashboard
├── Métricas do dia (OS abertas, em andamento, finalizadas)
├── Gráfico de receita mensal (mockado)
├── Fila visual rápida — carros por etapa
└── Notificações recentes

/oficina/os
├── Kanban — colunas por etapa, card com foto/placa/cliente/mecânico
├── Toggle para Tabela
├── Botão "Nova OS"
└── /oficina/os/{id}
    ├── Header: veículo + cliente + status atual
    ├── Stepper horizontal das etapas (etapa atual destacada)
    ├── Histórico de transições (timeline expandível)
    ├── Conteúdo da etapa atual
    │   ├── Check-in: checklist + upload de fotos
    │   ├── Diagnóstico: campo de texto rico
    │   ├── Aguardando Peças: orçamento, origem, recebimento, aprovação
    │   ├── Serviço: logs de progresso
    │   ├── Testes: resultado
    │   └── Finalização: checklist de saída + fotos
    └── Ações: Avançar | Regredir | Selecionar etapa

/oficina/clientes
├── Listagem com busca (nome, CPF, telefone)
└── /oficina/clientes/{id}: dados + veículos + histórico de OS

/oficina/veiculos
├── Listagem com busca (placa, modelo, cliente)
└── /oficina/veiculos/{id}: dados + histórico completo de OS

/oficina/estoque
├── Listagem de peças (nome, quantidade, valor unitário)
├── Alertas de estoque baixo
└── Entrada/saída manual

/oficina/financeiro
├── Resumo mensal (receitas por OS finalizadas)
├── Listagem de OS finalizadas com valores
└── Separação: estoque próprio vs. peças externas

/oficina/configuracoes
└── Nome, logo, contato, horário de funcionamento
```

### Portal do Cliente

```
/login
└── Aba Cliente: CPF + email
    └── Se múltiplas oficinas: lista de seleção

/cliente/veiculos
└── Cards dos veículos com badge de status se OS ativa

/cliente/os/{id}
├── Header: foto/ícone do veículo, placa, modelo
├── Timeline vertical das etapas (estilo rastreamento de encomenda)
│   └── Concluídas (marcadas) | Atual (destacada) | Próximas (cinza)
├── Status atual com descrição amigável
└── Orçamento aprovado (visível após aprovação interna)

/cliente/historico
└── Lista de OS anteriores com data, serviço resumido e valor total
```

---

## 5. Sistema de Design

### Paleta

| Token | Hex | Uso |
|---|---|---|
| `--color-void` | `#0F172A` | Background sidebar, headers portal oficina |
| `--color-ocean` | `#1E3A5F` | Elementos primários, nav ativa |
| `--color-spark` | `#3B82F6` | CTAs, links, badges ativos |
| `--color-surface` | `#F8FAFC` | Background geral, portal cliente |
| `--color-muted` | `#64748B` | Texto secundário, labels |
| `--color-border` | `#E2E8F0` | Bordas, divisores |

### Tipografia

| Papel | Fonte | Justificativa |
|---|---|---|
| Display | `Syne` | Geométrica, forte personalidade, incomum em admin panels — transmite precisão técnica |
| Corpo | `DM Sans` | Humanista, leitura confortável em tamanhos pequenos, mais calorosa que Inter |
| Utilitário | `JetBrains Mono` | Para OS IDs (`OS-2025-0047`), placas (`ABC-1234`), valores monetários |

### Elemento de assinatura — Trilho de Etapas
A linha do tempo da OS é o elemento central e identitário do sistema. Aparece nos dois portais com personalidades diferentes:
- **Portal Oficina:** stepper horizontal compacto no topo do detalhe da OS, com histórico expandível abaixo
- **Portal Cliente:** timeline vertical grande e emocional, estilo rastreamento de encomenda — comunica confiança e clareza

---

## 6. Estrutura de Arquivos

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── OficinaAuthController.php
│   │   │   └── ClienteAuthController.php
│   │   ├── Oficina/
│   │   │   ├── DashboardController.php
│   │   │   ├── OsController.php
│   │   │   ├── ClienteController.php
│   │   │   ├── VeiculoController.php
│   │   │   ├── EstoqueController.php
│   │   │   ├── FinanceiroController.php
│   │   │   └── ConfiguracoesController.php
│   │   └── Cliente/
│   │       ├── VeiculoController.php
│   │       ├── OsController.php
│   │       └── HistoricoController.php
│   └── Middleware/
│       ├── TenantMiddleware.php
│       ├── EnsureOficinaAuth.php
│       └── EnsureClienteAuth.php
│
├── Livewire/
│   ├── Oficina/
│   │   ├── Os/
│   │   │   ├── KanbanBoard.php
│   │   │   ├── OsDetalhe.php
│   │   │   ├── EtapaCheckin.php
│   │   │   ├── EtapaDiagnostico.php
│   │   │   ├── EtapaAguardandoPecas.php
│   │   │   ├── EtapaServico.php
│   │   │   ├── EtapaTestes.php
│   │   │   └── EtapaFinalizacao.php
│   │   └── Dashboard/
│   │       └── MetricasCard.php
│   └── Cliente/
│       ├── StatusTimeline.php
│       └── VeiculoCard.php
│
├── Models/                  ← shells vazios na Fase 1, prontos para Fase 2
│   ├── Tenant.php
│   ├── OrdemServico.php
│   ├── OsTransicao.php
│   ├── Cliente.php
│   ├── Veiculo.php
│   └── Peca.php
│
└── Services/
    └── Mock/
        ├── MockOsService.php
        ├── MockClienteService.php
        ├── MockVeiculoService.php
        ├── MockEstoqueService.php
        └── MockTenantService.php

resources/views/
├── layouts/
│   ├── oficina.blade.php
│   └── cliente.blade.php
├── auth/
│   └── login.blade.php
├── oficina/
│   ├── dashboard/
│   ├── os/
│   ├── clientes/
│   ├── veiculos/
│   ├── estoque/
│   ├── financeiro/
│   └── configuracoes/
└── cliente/
    ├── veiculos/
    ├── os/
    └── historico/

routes/
├── web.php
├── oficina.php
└── cliente.php
```

---

## 7. Critérios de Entrega — Fase 1

- [ ] Página de login com toggle Oficina / Cliente
- [ ] Portal Oficina: Dashboard + Kanban de OS + Detalhe de OS com todas as etapas
- [ ] Portal Cliente: timeline de status do carro
- [ ] Responsivo (mobile-first)
- [ ] Sem erros de console, sem estilos quebrados
- [ ] Arquitetura com camada Mock separada, pronta para swap na Fase 2
