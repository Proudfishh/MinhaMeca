# MinhaMeca — Módulo de Pendências Financeiras — Design Spec

**Data:** 2026-06-17  
**Stack:** Laravel 11 · PHP 8.2 · Blade · Alpine.js · Tailwind CSS v4  
**Fase:** 1 — Frontend com dados mockados

---

## 1. Escopo

Página dedicada "Pendências" na sidebar do portal da oficina (`/oficina/financeiro`), que permite:

1. Visualizar métricas financeiras resumidas (topo)
2. Listar pendências com filtro por status
3. Registrar pagamento de parcelas individuais (inline)
4. Criar nova pendência — vinculada a OS ou avulsa — com parcelamento

Rotas já existem. Três arquivos a tocar.

---

## 2. Modelo de dados (Fase 1 — Mock)

### 2.1 Estrutura de uma pendência

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | `string` | Identificador único, ex: `PEND-2025-001` |
| `tenant_id` | `int` | Multi-tenancy (Fase 1: sempre 1) |
| `tipo` | `'os'` \| `'avulso'` | Vinculada a OS ou criada manualmente |
| `os_id` | `string\|null` | Preenchido quando `tipo === 'os'` |
| `cliente_id` | `int\|null` | Referência ao cliente |
| `cliente` | `string` | Nome para exibição |
| `descricao` | `string` | Texto livre descrevendo a pendência |
| `valor_total` | `float` | Valor integral da pendência |
| `valor_pago` | `float` | Soma das parcelas já quitadas |
| `status` | `string` | Ver regras abaixo |
| `data_criacao` | `date` | Data de registro |
| `parcelas` | `array` | Lista de parcelas (mínimo 1) |

### 2.2 Estrutura de uma parcela

| Campo | Tipo | Descrição |
|---|---|---|
| `numero` | `int` | Posição: 1, 2, 3... |
| `valor` | `float` | Valor desta parcela |
| `vencimento` | `date` | Data de vencimento |
| `pago_em` | `date\|null` | Data do pagamento (`null` = não pago) |
| `forma_pagamento` | `string\|null` | Pix / Dinheiro / Cartão / Boleto |

### 2.3 Regras de status

| Status | Condição |
|---|---|
| `pago` | Todas as parcelas têm `pago_em` preenchido |
| `parcial` | Ao menos uma parcela paga E ao menos uma não paga (prevalece sobre `vencido`) |
| `vencido` | **Nenhuma** parcela paga E ao menos uma com `vencimento` < hoje |
| `pendente` | Nenhuma parcela paga E nenhuma vencida |
| `negociado` | Setado manualmente — sobrescreve a lógica automática |

O status é calculado no controller ao montar os dados (não armazenado como campo fixo no mock, exceto `negociado`).

### 2.4 Dados mockados — tenant 1

| ID | Cliente | cliente_id | Tipo | OS | Status | Parcelas |
|---|---|---|---|---|---|---|
| PEND-2025-001 | Carlos Henrique Souza | 1 | os | OS-2025-0047 | parcial | 3x — 1 paga (Pix), 2 abertas |
| PEND-2025-002 | Ana Paula Ferreira | 2 | os | OS-2025-0046 | vencido | 1x — vencida (2025-06-12) |
| PEND-2025-003 | Roberto Alves Lima | 3 | os | OS-2025-0049 | pago | 2x — ambas pagas |
| PEND-2025-004 | Auto Peças Ltda | 5 | avulso | null | pendente | 1x — vence 2025-07-10 |
| PEND-2025-005 | Carlos Henrique Souza | 1 | avulso | null | negociado | 2x — datas renegociadas |

---

## 3. Controller — `FinanceiroController::index()`

O controller injeta três serviços:

```php
public function __construct(
    private MockPendenciaService $pendenciaService,
    private MockOsService $osService,        // para popular o seletor de OS no modal
) {}

public function index()
{
    $tenantId   = session('tenant_id', 1);
    $pendencias = $this->pendenciaService->all($tenantId);
    $osList     = $this->osService->all($tenantId); // passado à view para o modal
    $hoje       = now()->toDateString();

    // Calcular status dinâmico
    foreach ($pendencias as &$p) {
        if ($p['status'] !== 'negociado') {
            $p['status'] = $this->calcularStatus($p['parcelas'], $hoje);
        }
        $p['valor_pago'] = collect($p['parcelas'])
            ->whereNotNull('pago_em')->sum('valor');
    }

    // Métricas
    $metricas = [
        'em_aberto'   => collect($pendencias)
            ->whereIn('status', ['pendente', 'parcial'])
            ->sum(fn($p) => $p['valor_total'] - $p['valor_pago']),
        'vencido'     => collect($pendencias)
            ->where('status', 'vencido')
            ->sum(fn($p) => $p['valor_total'] - $p['valor_pago']),
        'recebido_mes'=> collect($pendencias)->flatMap(fn($p) => $p['parcelas'])
            ->filter(fn($parc) => $parc['pago_em'] &&
                str_starts_with($parc['pago_em'], now()->format('Y-m')))
            ->sum('valor'),
        'ativas'      => collect($pendencias)
            ->whereNotIn('status', ['pago'])->count(),
    ];

    return view('oficina.financeiro.index', compact('pendencias', 'metricas', 'osList'));
}

private function calcularStatus(array $parcelas, string $hoje): string
{
    $pagas     = collect($parcelas)->whereNotNull('pago_em')->count();
    $total     = count($parcelas);
    $vencidas  = collect($parcelas)->whereNull('pago_em')
        ->filter(fn($p) => $p['vencimento'] < $hoje)->count();

    if ($pagas === $total)  return 'pago';
    if ($pagas > 0)         return 'parcial';
    if ($vencidas > 0)      return 'vencido';
    return 'pendente';
}
```

---

## 4. Página `/oficina/financeiro` — Layout

### 4.1 Header da página

```
Pendências  [badge: N ativas]              [+ Nova Pendência]
```

### 4.2 Métricas (4 cards)

```
┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐
│  Em aberto       │ │  Vencido         │ │  Recebido/mês    │ │  Pendências ativas│
│  R$ 2.840,00     │ │  R$ 630,00       │ │  R$ 1.200,00     │ │  8               │
└──────────────────┘ └──────────────────┘ └──────────────────┘ └──────────────────┘
```

Cores: Em aberto `ocean` · Vencido `#EF4444` · Recebido `#10B981` · Ativas `spark`

### 4.3 Barra de filtro + busca

```
[🔍 Buscar por cliente ou descrição...]    [Todas ▾]    [+ Nova Pendência]
```

Opções do filtro: Todas / Em aberto / Vencidas / Pagas / Negociadas

"Em aberto" agrupa `pendente` + `parcial`.

### 4.4 Cards de pendência

Cada card exibe:

```
┌──────────────────────────────────────────────────────────────────────────────┐
│ [badge status]  PEND-2025-001  ·  criado em 10/06         R$ 900,00 total   │
│ Carlos Henrique Souza  ·  [link OS-2025-0047]              R$ 300,00 em aberto│
│ "Saldo restante OS-2025-0047"                                                │
│                                                                              │
│  ── Parcelas ──────────────────────────────────────────────────────────────  │
│  ✓  Parcela 1   R$ 300,00   pago em 10/06   Pix                             │
│  ●  Parcela 2   R$ 300,00   vence 20/06     [Registrar pagamento]           │
│  ●  Parcela 3   R$ 300,00   vence 10/07     [Registrar pagamento]           │
└──────────────────────────────────────────────────────────────────────────────┘
```

**Badges de status:**

| Status | Cor | Label |
|---|---|---|
| pendente | cinza `#94A3B8` | Pendente |
| parcial | azul `#3B82F6` | Parcial |
| vencido | vermelho `#EF4444` | Vencido |
| pago | verde `#10B981` | Pago |
| negociado | amarelo `#F59E0B` | Negociado |

**Parcelas:**
- Paga: ✓ verde, `pago_em` formatado, forma de pagamento
- Em aberto: ● cinza, `vencimento` em vermelho se vencida
- Botão "Registrar pagamento" — abre mini-modal (ver 4.5)

**Ordenação padrão:** vencidas → parcial → pendente → negociado → pago; dentro de cada grupo, por vencimento mais próximo.

### 4.5 Mini-modal "Registrar pagamento"

Abre inline (pequeno modal) com três campos:

- **Valor** — preenchido com o valor da parcela, editável
- **Data do pagamento** — padrão: hoje
- **Forma de pagamento** — select: Pix / Dinheiro / Cartão de débito / Cartão de crédito / Boleto

Ao confirmar: marca `pago_em` e `forma_pagamento` na parcela, recalcula status e `valor_pago`, fecha modal, exibe toast "Pagamento registrado".

---

## 5. Modal "Nova Pendência" (2 etapas)

### Etapa 1 — Dados gerais

**Toggle:** `[● Vinculada a OS]  [○ Avulsa]`

**Modo OS:**
- Campo busca de OS (por ID ou nome do cliente) — lista as OS do tenant
- Ao selecionar: preenche cliente automaticamente, exibe valor total da OS
- Campo "Já pago" (R$) — calcula saldo automaticamente
- Descrição: pré-preenchida como "Saldo restante {os_id}", editável

**Modo avulso:**
- Campo cliente (texto livre ou busca na lista de clientes)
- Campo valor (R$)
- Campo descrição (texto livre)

### Etapa 2 — Parcelamento

- Valor a parcelar: exibido (calculado ou digitado)
- Seletor de quantidade de parcelas: 1 a 12
- Grade de parcelas gerada dinamicamente:
  - Valores distribuídos igualmente (centavos do arredondamento vão na última parcela)
  - Datas sugeridas: primeira = hoje + 30 dias; seguintes = +30 dias cada
  - Datas e valores individuais são editáveis
- Botão "Criar Pendência" — adiciona ao array Alpine, fecha modal, exibe toast

---

## 6. Alpine — estrutura de dados

```js
// Embutido em <script> via json_encode no padrão do projeto
window.__pendencias = [...];
window.__metricas   = {...};

function pendenciasPage() {
    return {
        pendencias: [],
        filtro: 'todas',
        busca: '',
        modalAberto: false,
        modalPagamento: { aberto: false, pendenciaId: null, parcelaNumero: null, valor: 0, data: '', forma: '' },
        novaForm: { /* etapa 1 e 2 */ },
        etapaModal: 1,

        get pendenciasFiltradas() { /* filtro + busca */ },
        registrarPagamento() { /* atualiza parcela no array */ },
        criarPendencia() { /* push no array */ },
    };
}
```

---

## 7. Arquivos a criar / modificar

| Arquivo | Ação |
|---|---|
| `app/Services/Mock/MockPendenciaService.php` | Criar |
| `app/Http/Controllers/Oficina/FinanceiroController.php` | Implementar `index()` |
| `resources/views/oficina/financeiro/index.blade.php` | Criar |

Rotas já declaradas em `routes/oficina.php` — sem alteração.

---

## 8. Restrições Fase 1

- Criação e registro de pagamento são client-side (Alpine) — não persistem no banco
- Não há paginação
- Não há exportação (PDF/Excel)
- Botão "+ Nova Pendência" no empty state também abre o modal
- Link da OS no card abre `/oficina/os/{id}` normalmente
