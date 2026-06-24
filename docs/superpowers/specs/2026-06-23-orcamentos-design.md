# Design: Módulo Orçamentos — MinhaMeca

**Data:** 2026-06-23  
**Status:** Aprovado para implementação

---

## Contexto

A aba Orçamentos foi extraída do módulo Estoque e virou um módulo próprio no menu principal. O objetivo é permitir que o mecânico crie, visualize e edite orçamentos de forma rápida e intuitiva no celular.

---

## 1. Navegação

### Menu lateral (desktop)
Entrada "Orçamentos" com ícone de documento, entre Estoque e Garantias.

### Bottom nav mobile
4 itens: Início · OS · ORC · Clientes  
"ORC" é o atalho para `/oficina/orcamentos`.

### Rotas
```
GET  /oficina/orcamentos          → index
GET  /oficina/orcamentos/novo     → wizard step 1 (contexto)
POST /oficina/orcamentos          → store (mock redirect com flash)
GET  /oficina/orcamentos/{id}     → show/edit
PUT  /oficina/orcamentos/{id}     → update (mock)
```

---

## 2. Tela de índice (`index.blade.php`)

### Métricas (grid)
- Mobile: 2 colunas
- Desktop: 4 colunas
- Cards: Total de orçamentos, Aprovados, Pendentes, Rascunhos

### Pesquisa
- Campo único aceita: placa (ex: ABC-1234), nome de cliente (ex: João), nome de veículo (ex: Civic)
- Filtra a lista em tempo real (Alpine.js `x-model` + computed getter)
- Normaliza acentos e maiúsculas na comparação

### Lista de orçamentos
- Ordenada do mais novo para o mais antigo (`criado_em` DESC)
- Mobile: cards com código, badge de status, cliente, valor total, data de validade, chevron
- Desktop: tabela com todas as colunas + coluna de ações
- Ao clicar no card/linha: navega para `GET /oficina/orcamentos/{id}` (edição)

### Status badges
- `aprovado` → verde (`#10B981`)
- `pendente` → amarelo/laranja (`#D97706`)
- `rascunho` → cinza (`#64748b`)

### FAB (mobile)
- `fixed bottom-[5rem] right-4` — acima do bottom nav
- Abre `GET /oficina/orcamentos/novo`

---

## 3. Criação — Wizard 2 passos

### Step 1: Contexto (`create.blade.php` — step 1)

Chips de contexto (seleção única, obrigatória):

| Chip | Ícone | Comportamento |
|------|-------|---------------|
| Cliente | 👤 | Abre autocomplete de clientes; preenche cliente + veículos do cliente no step 2 |
| Veículo | 🚗 | Busca por placa; preenche veículo + cliente vinculado no step 2 |
| OS aberta | 📋 | Lista OSes em aberto; vincula ORC à OS selecionada |
| Avulso | 📄 | Sem vínculo; orçamento standalone / exportável em PDF |

Botão "Continuar →" (azul sólido, full-width) navega para o step 2 passando o contexto via estado Alpine.js ou query param.

**Data de validade**: campo de data exibido no step 1, abaixo dos chips. Valor padrão: hoje + 7 dias. Formato: DD/MM/AAAA.

---

### Step 2: Montagem do orçamento (`create.blade.php` — step 2)

#### Header da tela
Mostra o contexto selecionado: nome do cliente e/ou placa do veículo.  
Link "← voltar" retorna ao step 1.

#### Seções de itens

Cada seção é um card com borda azul translúcida quando ativa, borda tracejada cinza quando inativa.

##### Seção Peças (sempre visível, sempre ativa)
```
┌─────────────────────────────────┐
│ 🔧 Peças   [2 itens]  [+ Adicionar] │
├─────────────────────────────────┤
│ Pastilha freio (par)      R$ 170 │
│ Fluido DOT4 ×1            R$ 28  │
└─────────────────────────────────┘
```

- "+ Adicionar" → abre **bottom sheet de peças** (ver seção 3.1)
- Cada item mostra nome, quantidade e preço; toque longo ou ícone ✕ para remover

##### Seções de serviço (Mão de obra, Retífica, Outros)

**Estado inicial (inativa):**
```
┌──────────────────────────────────┐
│ 🛠 Mão de obra        [+ Adicionar] │
└──────────────────────────────────┘  ← borda tracejada cinza
```

**Após "+ Adicionar" (modo simples):**
```
┌──────────────────────────────────┐
│ 🛠 Mão de obra        [Detalhar ›] │
├──────────────────────────────────┤
│ R$  [280,00            ]          │
└──────────────────────────────────┘
```
- Campo de valor único
- **[Detalhar ›]** = chip azul: `bg-blue-500/10 text-blue-500 rounded-md px-2 py-0.5 text-xs font-bold`

**Após "Detalhar ›" (modo itemizado):**
```
┌───────────────────────────────────────┐
│ 🛠 Mão de obra  [detalhada]  ← simples │
├───────────────────────────────────────┤
│ Troca de pastilhas              R$ 120 ✕ │
│ Sangria do freio                R$ 80  ✕ │
│ ┌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌┐    │
│ │ [+] Novo serviço              │    │  ← borda tracejada azul
│ └╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌╌┘    │
└───────────────────────────────────────┘
```
- "← simples" volta para campo único (descarta itens, exibe confirmação se houver itens)
- "+ Novo serviço" (linha tracejada azul): `border: 1.5px dashed rgba(59,130,246,0.35); border-radius: 7px; padding: 6px 9px` com ícone `+` quadrado azul à esquerda (`16×16px, border-radius: 4px, bg-blue-500/12`)
- Tocar "Novo serviço" → abre um mini bottom sheet com campos: Descrição + Valor + botão "Adicionar"

**Regras de ativação:**
- Retífica e Outros têm ícone ✕ no header para desativar a seção inteira
- Mão de obra não pode ser desativada após ativada sem limpar o formulário (exibir confirmação)

#### Barra de total (fixa no bottom, acima do bottom-nav)
```
┌────────────────────────┐
│ TOTAL        R$ 828,00 │
│           [Salvar ORC] │
└────────────────────────┘
```
- Background `#0f172a` (void)
- Total calculado em tempo real (Alpine.js computed)
- "Salvar ORC" → `POST /oficina/orcamentos` → redireciona para index com mensagem flash

---

### 3.1 Bottom sheet de peças

Abre sobre a tela de montagem. Backdrop escuro semi-transparente.

```
╔══════════════════════════════════╗
║  ━━━━  (handle)                  ║
║  Adicionar peça                  ║
║                                  ║
║  🔍 Buscar no estoque…           ║
║  ┌──────────────────────────┐    ║
║  │ Pastilha freio (par)      │    ║
║  │ Freios · 4 em estoque R$85│+Add║
║  │ Fluido DOT4               │    ║
║  │ Freios · 5 em estoque R$28│+Add║
║  └──────────────────────────┘    ║
║  ──────────── ou ─────────────   ║
║  Nome da peça                    ║
║  [____________________]          ║
║  Qtd  [1]   Valor [R$ ____]      ║
║  [    Adicionar peça     ]       ║ ← azul sólido
╚══════════════════════════════════╝
```

- Campo de busca faz pesquisa nos itens de estoque (filtra por nome)
- Ao tocar "+ Add" num resultado do estoque: incrementa contador daquele item e fecha o sheet
- Entrada manual: nome livre + quantidade + valor unitário
- Ao adicionar manualmente: item aparece na seção Peças com flag "manual" (sem vínculo de estoque)
- Fechar: swipe down ou tap no backdrop

---

## 4. Tela de edição (`show.blade.php` ou reutilizar `create.blade.php` com `$mode = 'edit'`)

Mesma interface do step 2 da criação, mas:
- Dados do orçamento já preenchidos
- Campo adicional no final: **Vincular a OS** — dropdown das OSes abertas do mesmo cliente/veículo
- Botão "Salvar alterações" → `PUT /oficina/orcamentos/{id}`
- Botão "Excluir orçamento" (vermelho, confirma antes)

---

## 5. Arquitetura de dados mock

### MockOrcamentosService
Retorna array de orçamentos com:
```php
[
  'id'          => 1,
  'codigo'      => 'ORC-2026-001',
  'cliente'     => 'João Silva',
  'veiculo'     => 'Civic 2019',
  'placa'       => 'ABC-1234',
  'status'      => 'aprovado', // aprovado | pendente | rascunho
  'total'       => 1250.00,
  'validade'    => '2026-07-01',
  'criado_em'   => '2026-06-20',
  'os_vinculada'=> 'OS-2026-045', // null se avulso
  'itens'       => [
    ['tipo' => 'peca', 'nome' => 'Pastilha freio', 'qtd' => 2, 'valor' => 85.00],
    ['tipo' => 'mao_de_obra', 'modo' => 'simples', 'valor' => 280.00],
  ],
]
```

### MockEstoqueService (já existente)
Reutilizado no bottom sheet de busca de peças. Retorna `id, nome, categoria, preco, estoque`.

---

## 6. Componentes Alpine.js

### `orcamentos-index`
```js
x-data="{
  busca: '',
  orcamentos: [...],  // passado via PHP
  get filtrados() {
    const q = this.normalizar(this.busca);
    if (!q) return this.orcamentos;
    return this.orcamentos.filter(o =>
      this.normalizar(o.cliente).includes(q) ||
      this.normalizar(o.veiculo).includes(q) ||
      this.normalizar(o.placa).includes(q)
    );
  },
  normalizar(s) { return s.toLowerCase().normalize('NFD').replace(/\p{M}/gu, ''); }
}"
```

### `orcamento-create`
```js
x-data="{
  step: 1,
  contexto: null, // 'cliente' | 'veiculo' | 'os' | 'avulso'
  clienteSelecionado: null,
  veiculoSelecionado: null,
  osSelecionada: null,
  pecas: [],
  maoDeObra: { ativa: false, modo: 'simples', valor: '', itens: [] },
  reifica: { ativa: false, modo: 'simples', valor: '', itens: [] },
  outros: { ativa: false, modo: 'simples', valor: '', itens: [] },
  sheetPecasAberto: false,
  get total() { /* soma peças + serviços */ },
  adicionarPeca(item) { ... },
  removerPeca(index) { ... },
  detalharSecao(secao) { secao.modo = 'detalhado'; },
  resumirSecao(secao) { secao.modo = 'simples'; secao.itens = []; },
  adicionarItemServico(secao, desc, valor) { ... },
}"
```

---

## 7. Considerações de implementação

- **Não há persistência real**: todas as ações de POST/PUT fazem mock redirect com `session()->flash('success', '...')`
- **Reutilizar layout**: `<x-layouts.oficina>` com `$title = 'Orçamentos'`
- **Bottom sheet**: padrão já estabelecido no projeto — `fixed inset-0 z-50` backdrop + `fixed bottom-0 left-0 right-0 z-50 rounded-t-2xl`
- **FAB**: `fixed bottom-[5rem] right-4 md:hidden z-40`
- **Validade padrão**: calcular no controller `now()->addDays(7)->format('Y-m-d')`
- **Formatação de moeda**: Alpine getter formata como `R$ 1.250,00` (pt-BR)
