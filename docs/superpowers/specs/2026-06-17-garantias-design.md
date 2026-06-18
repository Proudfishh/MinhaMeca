# MinhaMeca — Módulo de Garantias — Design Spec

**Data:** 2026-06-17  
**Stack:** Laravel 11 · PHP 8.2 · Blade · Alpine.js · Tailwind CSS v4  
**Fase:** 1 — Frontend com dados mockados

---

## 1. Escopo

Página dedicada "Garantias" na sidebar do portal da oficina (`/oficina/garantias`), que permite:

1. Visualizar e agir sobre follow-ups de pós-atendimento pendentes
2. Monitorar garantias próximas do vencimento
3. Listar e filtrar todas as garantias por status
4. Acionar uma garantia (abre OS de retrabalho vinculada)
5. Registrar garantias manualmente (para OS fora do sistema)

**Regra central:** garantias são criadas automaticamente ao finalizar uma OS (etapa `finalizacao`). O prazo padrão é 90 dias — configurável em Configurações (não implementado na Fase 1). O prazo nunca se renova: mesmo que a garantia seja acionada e uma OS de retrabalho seja aberta, `data_vencimento` permanece baseada na OS original.

---

## 2. Modelo de dados (Fase 1 — Mock)

### 2.1 Estrutura de uma garantia

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | `string` | Ex: `GARAN-2026-001` |
| `tenant_id` | `int` | Multi-tenancy (Fase 1: sempre 1) |
| `os_id` | `string` | OS de origem |
| `cliente_id` | `int` | Referência ao cliente |
| `cliente` | `string` | Nome para exibição |
| `veiculo_id` | `int` | Referência ao veículo |
| `veiculo` | `string` | Descrição para exibição |
| `data_entrega` | `date` | Data de finalização da OS |
| `data_vencimento` | `date` | `data_entrega` + 90 dias — imutável |
| `status` | `string` | Calculado dinamicamente (ver 2.3) |
| `os_retrabalho_id` | `string\|null` | Preenchido ao acionar garantia |
| `follow_ups` | `array` | 3 follow-ups por garantia (ver 2.2) |

### 2.2 Estrutura de um follow-up

| Campo | Tipo | Descrição |
|---|---|---|
| `tipo` | `string` | `7d` \| `30d` \| `pre_vencimento` |
| `label` | `string` | Ex: "7 dias após entrega" |
| `data_prevista` | `date` | Ver cadência abaixo |
| `feito_em` | `date\|null` | Preenchido ao marcar como feito |
| `mensagem_modelo` | `string` | Texto pré-escrito para copiar |

**Cadência dos follow-ups:**

| Tipo | `data_prevista` |
|---|---|
| `7d` | `data_entrega` + 7 dias |
| `30d` | `data_entrega` + 30 dias |
| `pre_vencimento` | `data_vencimento` - 10 dias |

**Mensagens modelo por tipo:**
- `7d`: "Olá, [cliente]! Tudo bem? Passando para saber se o [veículo] está tudo certo após o serviço realizado. Qualquer dúvida estamos à disposição! 😊"
- `30d`: "Olá, [cliente]! Já faz um mês desde que atendemos o [veículo]. Está tudo bem com o veículo? Nossa garantia de 90 dias segue válida. Conte com a gente!"
- `pre_vencimento`: "Olá, [cliente]! A garantia do serviço realizado no [veículo] vence em breve (dia [data_vencimento]). Caso perceba qualquer problema, entre em contato antes do prazo!"

### 2.3 Regras de status (calculado dinamicamente no controller)

| Status | Condição |
|---|---|
| `ativa` | `data_vencimento >= hoje + 11 dias` E `os_retrabalho_id = null` |
| `vencendo` | `0 < data_vencimento - hoje <= 10 dias` E `os_retrabalho_id = null` |
| `expirada` | `data_vencimento < hoje` E `os_retrabalho_id = null` |
| `acionada` | `os_retrabalho_id != null` (independe da data) |

### 2.4 Dados mockados — tenant 1

| ID | Cliente | Veículo | OS | Data entrega | Status | Follow-ups |
|---|---|---|---|---|---|---|
| GARAN-2026-001 | Carlos Henrique Souza | Honda Civic 2019 · ABC-1234 | OS-2025-0047 | 2026-05-20 | ativa | 7d feito, 30d pendente |
| GARAN-2026-002 | Ana Paula Ferreira | Toyota Corolla 2021 · DEF-5678 | OS-2025-0048 | 2026-05-25 | ativa | 7d feito, 30d feito |
| GARAN-2026-003 | Roberto Alves Lima | Volkswagen Gol 2018 · GHI-9012 | OS-2025-0049 | 2026-06-07 | vencendo | 7d feito, 30d pendente, pré-vencimento pendente |
| GARAN-2026-004 | Fernanda Costa | Fiat Palio 2016 · JKL-3456 | OS-2025-0050 | 2026-03-01 | expirada | todos feitos |
| GARAN-2026-005 | Auto Peças Ltda | Fiat Strada 2020 · MNO-7890 | OS-2025-0051 | 2026-05-10 | acionada | 7d feito, 30d feito |

> GARAN-2026-003: `data_entrega = 2026-06-07` → `data_vencimento = 2026-09-05` — mas para fins de mock na Fase 1, o status `vencendo` é fixado diretamente no serviço (vence em 5 dias a partir de 2026-06-17 = 2026-06-22).

---

## 3. Controller — `GarantiaController::index()`

```php
public function __construct(
    private MockGarantiaService $garantiaService,
) {}

public function index()
{
    $tenantId  = session('tenant_id', 1);
    $garantias = $this->garantiaService->all($tenantId);
    $hoje      = now()->toDateString();

    foreach ($garantias as &$g) {
        if (!$g['os_retrabalho_id']) {
            $g['status'] = $this->calcularStatus($g['data_vencimento'], $hoje);
        } else {
            $g['status'] = 'acionada';
        }
        $g['dias_restantes'] = (int) now()->diffInDays($g['data_vencimento'], false);
    }
    unset($g);

    $followUpsPendentes = collect($garantias)
        ->flatMap(fn($g) => collect($g['follow_ups'])->map(fn($f) => array_merge($f, [
            'garantia_id'  => $g['id'],
            'cliente'      => $g['cliente'],
            'veiculo'      => $g['veiculo'],
            'os_id'        => $g['os_id'],
        ])))
        ->filter(fn($f) => !$f['feito_em'] && $f['data_prevista'] <= $hoje)
        ->sortBy('data_prevista')
        ->values()
        ->all();

    $metricas = [
        'ativas'   => collect($garantias)->where('status', 'ativa')->count(),
        'vencendo' => collect($garantias)->where('status', 'vencendo')->count(),
        'expiradas'=> collect($garantias)->where('status', 'expirada')->count(),
        'acionadas'=> collect($garantias)->where('status', 'acionada')->count(),
    ];

    return view('oficina.garantias.index', compact('garantias', 'followUpsPendentes', 'metricas'));
}

private function calcularStatus(string $dataVencimento, string $hoje): string
{
    $diff = (int) now()->diffInDays($dataVencimento, false);
    if ($diff < 0)        return 'expirada';
    if ($diff <= 10)      return 'vencendo';
    return 'ativa';
}
```

---

## 4. Layout da página

### 4.1 Header

```
Garantias   [badge: N ativas]
```

Sem botão no header principal — o "+ Registrar manualmente" fica no topo do Bloco 3.

### 4.2 Bloco 1 — Follow-ups pendentes

Aparece **somente** quando há follow-ups com `data_prevista <= hoje` e `feito_em = null`. Sem empty state — o bloco some completamente quando não há pendências.

```
┌─────────────────────────────────────────────────────────────────┐
│ 📋 Follow-ups pendentes (N)                                     │
│                                                                 │
│ ┌──────────────────────────────────────────────────────────┐   │
│ │ [badge 30 dias]  Carlos · Honda Civic · OS-2025-0047     │   │
│ │ "Olá, Carlos! Já faz um mês..."         [Copiar] [Feito] │   │
│ └──────────────────────────────────────────────────────────┘   │
│ ┌──────────────────────────────────────────────────────────┐   │
│ │ [badge Pré-venc.] Roberto · VW Gol · OS-2025-0049        │   │
│ │ "Olá, Roberto! A garantia vence em breve..." [Copiar][Feito]│ │
│ └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

Cores dos badges:
- `7d` → cinza `#94A3B8`
- `30d` → azul `#3B82F6`
- `pre_vencimento` → âmbar `#F59E0B`

Ao clicar **Feito**: `feito_em = hoje`, card some com animação, toast "Follow-up registrado".  
Ao clicar **Copiar**: copia `mensagem_modelo` para o clipboard, ícone muda para ✓ por 2s.

### 4.3 Bloco 2 — Vencendo em breve

Aparece **somente** quando há garantias com status `vencendo`. Faixa âmbar de alerta com cards compactos lado a lado.

```
⚠️ Vencendo em breve
┌─────────────────────┐  ┌─────────────────────┐
│ Roberto · VW Gol    │  │ ...                 │
│ OS-2025-0049        │  │                     │
│ Vence em 5 dias     │  │                     │
│ [Acionar garantia]  │  │                     │
└─────────────────────┘  └─────────────────────┘
```

### 4.4 Bloco 3 — Todas as garantias

```
Todas as garantias  [+ Registrar manualmente]
[🔍 Buscar...]  [Todas][Ativas][Vencendo][Expiradas][Acionadas]
```

Cada card de garantia exibe:
- Badge de status (cor conforme tabela)
- ID da garantia + link para OS de origem
- Cliente e veículo
- Data de entrega e data de vencimento
- Dias restantes (ou "Expirada há X dias" em vermelho)
- Progresso dos 3 follow-ups: três ícones (✓ feito / ● pendente / — futuro)
- Botão **Acionar garantia** (desabilitado e com tooltip se já acionada ou expirada)
- Se `acionada`: badge com link para OS de retrabalho

**Ordenação:** `vencendo` → `ativa` (por `data_vencimento` asc) → `acionada` → `expirada`

**Badges de status:**

| Status | Cor | Label |
|---|---|---|
| `ativa` | verde `#10B981` | Ativa |
| `vencendo` | âmbar `#F59E0B` | Vencendo |
| `expirada` | vermelho `#EF4444` | Expirada |
| `acionada` | violeta `#7C3AED` | Acionada |

---

## 5. Modal "Acionar garantia"

Campos:
- **Descrição do problema** (textarea, obrigatório)
- **Data de abertura** (date, padrão: hoje)

Ao confirmar:
- `os_retrabalho_id` recebe ID gerado: `OS-RET-` + timestamp
- `status` muda para `acionada`
- Toast: "Garantia acionada — OS de retrabalho criada"
- Card exibe badge **Acionada** com link `#` (Fase 1)

Restrição Fase 1: OS de retrabalho não é integrada ao módulo de OS — apenas o ID é registrado na garantia.

---

## 6. Modal "Registrar manualmente"

Campos:
- **Cliente** (texto livre, obrigatório)
- **Veículo** (texto livre, obrigatório)
- **Referência OS** (texto livre, opcional)
- **Data de entrega** (date, obrigatório)
- **Prazo de garantia em dias** (number, padrão: 90)

Ao confirmar:
- `data_vencimento = data_entrega + prazo`
- Follow-ups gerados automaticamente com datas calculadas
- Status calculado dinamicamente
- Adicionado ao array Alpine; toast "Garantia registrada"

---

## 7. Alpine — estrutura

```js
window.__garantias         = [...];   // array completo
window.__followUpsPendentes = [...];  // follow-ups vencidos não feitos
window.__metricas          = {...};

function garantiasPage() {
    return {
        garantias: [],
        followUps: [],
        metricas: {},
        filtro: 'todas',
        busca: '',

        // Modal acionar
        maAberto: false,
        maGarantiaId: null,
        maDescricao: '',
        maData: '',

        // Modal registrar
        mrAberto: false,
        mr: { cliente:'', veiculo:'', os_ref:'', data_entrega:'', prazo: 90 },

        get garantiasFiltradas() { /* filtro + busca + ordenação */ },
        get filtros() { /* contadores por status */ },

        marcarFollowUpFeito(garantiaId, tipo) { /* feito_em = hoje */ },
        copiarMensagem(texto) { /* clipboard API */ },
        acionarGarantia() { /* os_retrabalho_id, status = acionada */ },
        registrarManualmente() { /* push no array */ },
        recalcularStatus(dataVencimento) { /* lógica de dias */ },
    };
}
```

---

## 8. Arquivos a criar / modificar

| Arquivo | Ação |
|---|---|
| `app/Services/Mock/MockGarantiaService.php` | Criar |
| `app/Http/Controllers/Oficina/GarantiaController.php` | Criar |
| `resources/views/oficina/garantias/index.blade.php` | Criar |
| `routes/oficina.php` | Modificar — adicionar rota |
| `resources/views/components/layouts/oficina.blade.php` | Modificar — sidebar |

Rota nova:
```
GET /oficina/garantias  →  oficina.garantias.index  →  GarantiaController@index
```

Sidebar: item entre "Estoque" e "Pendências", ícone de escudo.

---

## 9. Restrições Fase 1

- Criação, acionamento e follow-ups são client-side (Alpine) — não persistem
- Prazo padrão (90 dias) é hardcoded — não lê de Configurações ainda
- OS de retrabalho não é integrada ao módulo de OS
- Não há envio real de mensagens — apenas cópia de texto modelo
- Não há paginação
