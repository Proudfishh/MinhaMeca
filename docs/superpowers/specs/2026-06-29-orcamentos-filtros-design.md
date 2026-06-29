# Filtros na listagem de Orçamentos — Design

**Data:** 2026-06-29
**Tela:** `resources/views/oficina/orcamentos/index.blade.php`

## Objetivo

Adicionar filtros à listagem de orçamentos. Os filtros de status mais usados
(Rascunho, Pendente, Aprovado) ficam sempre visíveis como chips; um botão
"Filtros" abre um painel com os filtros avançados (divulgação progressiva).

Toda a filtragem é **client-side** (Alpine.js), reaproveitando o array
`window.__orcamentos` já injetado na view. **Nenhuma mudança no backend** —
o `OrcamentosController@index` continua igual.

## Layout

Abaixo da busca de texto existente, uma linha de controles:

```
[ 🔎 Buscar por cliente, placa ou veículo…            ]
[ Todos ] [ Rascunho ] [ Pendente ] [ Aprovado ]   [ Filtros ² ▾ ]
```

- **Chips de status** — seleção única, filtram imediatamente. "Todos" = sem
  filtro de status (estado inicial). Cores seguem o `statusInfo()` já existente
  (aprovado verde, pendente âmbar, rascunho cinza); o chip ativo usa fundo
  sólido na cor do status, inativo fica branco com borda.
- **Botão "Filtros"** — alinhado à direita, estilo `spark` (azul claro). Mostra
  um contador com a quantidade de filtros avançados ativos (badge). Sem filtros
  ativos, o badge some.

### Abertura do painel

Mesmo conteúdo interno, invólucro diferente por breakpoint:

- **Mobile (`md:hidden`)** — bottom sheet vindo de baixo, com backdrop
  `rgba(0,0,0,0.5)` (z-40) e folha branca `rounded-t-2xl` (z-50), igual aos
  bottom sheets já usados em `orcamentos/show.blade.php`. Grip no topo.
- **Desktop (`hidden md:block`)** — painel dropdown (card branco `rounded-xl`,
  borda + sombra) posicionado logo abaixo do botão "Filtros", aberto/fechado
  pelo mesmo estado Alpine. Fecha ao clicar fora.

## Seções do painel

Cada seção é seleção única; a primeira opção é sempre "sem filtro".

| Seção | Opções | Campo / lógica |
|-------|--------|----------------|
| **Validade** | Todas · Vencendo (≤7d) · Vencidos · Este mês | `validade` vs. hoje |
| **Faixa de valor** | Qualquer · Até R$ 200 · R$ 200–500 · R$ 500+ | `total` |
| **Vínculo com OS** | Todos · Com OS · Avulso | presença de `os_vinculada` |
| **Período de criação** | Qualquer · Últimos 7 dias · Este mês | `criado_em` |
| **Ordenar por** | Mais recentes · Maior valor · Validade próxima | ordena o resultado |

Opções renderizadas como "pills" clicáveis (mesmo visual do mockup aprovado:
fundo `#f8fafc` com borda; selecionado em azul `rgba(59,130,246,0.1)` + borda
`#3B82F6`).

### Semântica dos filtros (datas relativas a "hoje")

- **Validade**
  - *Vencendo (≤7d)*: `validade >= hoje` e `validade <= hoje + 7 dias`
  - *Vencidos*: `validade < hoje`
  - *Este mês*: `validade` no mês/ano corrente
- **Faixa de valor**: `Até 200` → `total <= 200`; `200–500` → `200 < total <= 500`;
  `500+` → `total > 500`
- **Vínculo**: *Com OS* → `os_vinculada` preenchido; *Avulso* → vazio/nulo
- **Período de criação**: *Últimos 7 dias* → `criado_em >= hoje - 7d`;
  *Este mês* → `criado_em` no mês/ano corrente
- **Ordenar por**: *Mais recentes* → `criado_em` desc (padrão atual);
  *Maior valor* → `total` desc; *Validade próxima* → `validade` asc

Datas no formato `YYYY-MM-DD` (string) — comparação direta de string funciona
para igualdade/ordem; "hoje" e janelas são calculadas via `Date` em JS.

## Aplicar / Limpar

O painel trabalha com **estado em rascunho** (`draft`), separado do estado
**aplicado** (`ativo`):

- Tocar nas pills altera só o `draft`.
- Botão **"Aplicar (N)"** — `N` é a contagem ao vivo de orçamentos que batem
  com `draft` + busca + status. Ao confirmar, copia `draft → ativo` e fecha.
- Botão **"Limpar"** — reseta o `draft` para os padrões (sem filtro).
- Ao reabrir o painel, `draft` é sincronizado a partir do `ativo` (não perde o
  que já estava aplicado).
- Os **chips de status filtram imediatamente** (ficam fora do painel, não passam
  pelo rascunho).

O badge do botão "Filtros" conta quantas das 4 seções de filtro avançado
(validade, valor, vínculo, período) estão diferentes do padrão no estado
**aplicado**. "Ordenar por" não conta no badge (é ordenação, não filtro).

## Estado vazio

Quando os filtros/busca não retornam nada, mostrar a mensagem de "nenhum
resultado" já existente, ajustada para mencionar filtros além da busca
(ex: "Nenhum orçamento com esses filtros"). Botão/atalho para **limpar tudo**
(busca + status + filtros avançados).

## Implementação (componente Alpine `orcamentosIndex`)

Estende o componente atual. Novos campos de estado:

```js
statusAtivo: 'todos',          // chip de status
painelAberto: false,           // sheet/dropdown
ativo:  { validade:'todas', valor:'qualquer', vinculo:'todos', periodo:'qualquer', ordenar:'recentes' },
draft:  { ...mesma forma... },
```

- `get filtrados()` passa a aplicar, na ordem: busca → status → validade →
  valor → vínculo → período; depois ordena conforme `ativo.ordenar`.
- `get previaCount()` aplica busca + status + `draft` (sem alterar `ativo`)
  e retorna o total — usado no botão "Aplicar (N)".
- `get filtrosAtivosCount()` conta as seções avançadas diferentes do padrão
  em `ativo` — usado no badge.
- `abrirPainel()` sincroniza `draft = {...ativo}` e abre.
- `aplicar()` faz `ativo = {...draft}` e fecha.
- `limparDraft()` reseta `draft` para os padrões.
- `limparTudo()` reseta busca, status e `ativo`.
- Helpers de data (`hoje`, janelas) em métodos puros para manter o getter legível.

A lista (cards mobile + tabela desktop) continua iterando `filtrados`, sem
mudança estrutural.

## Fora de escopo (YAGNI)

- Persistir filtros na URL ou em `localStorage`.
- Filtro por cliente/veículo específico (já coberto pela busca de texto).
- Slider de valor (substituído por faixas prontas).
- Multi-seleção de status (seleção única é suficiente para o caso de uso).
