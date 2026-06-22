# Spec — OS Mobile: Lista Agrupada + Bottom Nav

**Data:** 2026-06-21
**Escopo:** Adaptação responsiva da tela de Ordens de Serviço (`/oficina/os`) para dispositivos móveis
**Fase:** 1 (frontend mockado — sem lógica de backend real)

---

## Contexto

A tela de OS no desktop está finalizada e aprovada: Kanban com 6 colunas + toggle para visão em Tabela. No mobile, o layout atual sofre de três problemas:

1. A sidebar ocupa 64px de largura mesmo colapsada — espaço horizontal crítico no celular.
2. O Kanban horizontal exige scroll lateral por 6 colunas, experiência ruim para gestão ativa.
3. A tabela com 8 colunas é ilegível em telas estreitas.

**Caso de uso primário no mobile:** gestão ativa em campo — mudar etapa, consultar OS de um cliente, abrir nova OS.

**Premissa:** o desktop não muda. Toda alteração é exclusivamente para viewports abaixo de `md` (768px).

---

## O que muda

### 1. Sidebar — `recursos/views/components/layouts/oficina.blade.php`

- Adicionar `hidden md:flex` na tag `<aside>` — sidebar desaparece no mobile.
- O layout passa a usar 100% da largura disponível no mobile.

### 2. Bottom Navigation Bar (nova — só mobile)

Adicionada ao final do `<body>` no layout, dentro de um bloco `md:hidden` claramente comentado:

```
{{-- ===== MOBILE BOTTOM NAV (md:hidden) ===== --}}
{{-- Para transição futura para Drawer (opção B): trocar este bloco por
     um <button> hambúrguer na topbar + drawer Alpine. --}}
```

**Estrutura:** barra fixa no rodapé, fundo `#0F172A`, 4 itens:

| Item | Ícone | Rota |
|------|-------|------|
| Dashboard | home | `oficina.dashboard` |
| OS | clipboard-list | `oficina.os.index` |
| Clientes | users | `oficina.clientes.index` |
| Mais | menu / três linhas | abre sheet |

- Item ativo: ícone e label em `#3B82F6`, fundo `#1E3A5F` arredondado.
- Itens inativos: ícone e label em `rgba(255,255,255,0.35)`.
- "Mais" abre um **bottom sheet** Alpine (`x-show` / `x-transition`) com os módulos restantes listados verticalmente: Veículos, Estoque, Garantias, Pendências, Configurações. Toque fora fecha o sheet.

### 3. Tela de OS — `resources/views/oficina/os/index.blade.php`

#### Header mobile

- O header atual (toggle Kanban/Tabela + botão Nova OS) recebe `hidden md:flex`.
- Substituído por um header mobile (`md:hidden`) com:
  - Título "Ordens de Serviço" + contagem de OS
  - Botão `+` Nova OS no canto direito (mesmo destino: `route('oficina.os.create')`)

#### Vista mobile — lista agrupada por etapa

Bloco `md:hidden` logo abaixo do header, implementado em Alpine:

```
x-data="{ grupos: { checkin: true, diagnostico: true, pecas: true, servico: true, testes: true, finalizacao: true } }"
```

**Por etapa (na ordem do fluxo):** Check-in → Diagnóstico → Aguardando Peças → Serviço → Testes → Finalização.

Etapas sem OS são renderizadas mas com estado vazio (mesma lógica do Kanban desktop).

**Cabeçalho de grupo:**
- Fundo com cor da etapa em 8% de opacidade (`{cor}14`)
- Ponto colorido + label da etapa em uppercase bold + badge com contagem
- Chevron indicando estado expandido/colapsado
- Tap no cabeçalho: `grupos.{key} = !grupos.{key}`
- Começa **expandido** por padrão

**Card de OS (mobile):**
- Borda esquerda 3px com cor da etapa (igual ao card desktop)
- Borda ao redor 1px `#E2E8F0`
- Border-radius `8px`
- Conteúdo:
  - ID da OS (font-mono, 10px, muted)
  - Nome do cliente (font-bold, 14px, void)
  - Modelo do veículo + placa (12px, muted)
  - Footer: avatar inicial do mecânico (14×14px, ocean) + nome curto | data de previsão (font-mono)
- Tap no card → `route('oficina.os.show', $os['id'])`

**Kanban e Tabela:** recebem `hidden md:block` / `hidden md:flex` — continuam no DOM mas invisíveis no mobile.

---

## Breakpoints

| Viewport | Layout |
|----------|--------|
| `< 768px` (mobile) | Bottom nav + lista agrupada |
| `≥ 768px` (desktop/tablet) | Sidebar + Kanban/Tabela (sem mudança) |

---

## Preparação para transição futura (Drawer — opção B)

Para trocar bottom nav por drawer lateral no futuro:

1. Remover o bloco `{{-- MOBILE BOTTOM NAV --}}` do layout.
2. Adicionar `<button>` hambúrguer na topbar (já existe lógica `sidebarOpen` no Alpine do layout).
3. Tornar a sidebar visível no mobile com `x-show` + overlay escuro.
4. Nenhuma alteração necessária na view de OS.

---

## Arquivos alterados

| Arquivo | Alteração |
|---------|-----------|
| `resources/views/components/layouts/oficina.blade.php` | `hidden md:flex` na sidebar + bloco de bottom nav mobile |
| `resources/views/oficina/os/index.blade.php` | Header mobile + lista agrupada por etapa |

---

## Fora de escopo

- Outras telas (Clientes, Estoque, etc.) — cada uma será avaliada separadamente.
- Lógica de mudança de etapa direto do card mobile — Fase 2.
- PWA / instalação como app — Fase 2.
