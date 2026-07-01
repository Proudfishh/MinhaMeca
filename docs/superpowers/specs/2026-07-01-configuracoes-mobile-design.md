# Configurações — otimização para mobile

**Data:** 2026-07-01
**Módulo:** Oficina › Configurações
**Arquivo alvo:** `resources/views/oficina/configuracoes/index.blade.php`

## Objetivo

A tela de Configurações é desktop-first e quebra no celular: a barra de abas
estoura a largura, as tabelas (Membros, Faturas) não cabem, as ações de membro
dependem de `hover` (inexistente no toque) e o grid de 3 planos do modal de
upgrade aperta. Tornar a tela usável no mobile **sem alterar o desktop**.

## Princípio

Tudo é **reflow responsivo**: versão mobile via `md:hidden`, versão desktop via
`hidden md:…`. Nenhuma mudança de lógica, dados ou fluxo — continua tudo
mock/client-side no `configPage()`. Única adição de comportamento: um bottom
sheet para as ações do membro.

## Decisões confirmadas com o usuário

1. **Navegação entre seções (mobile):** grade de ícones **2×2** (não barra
   rolável nem dropdown).
2. **Ações do membro (mobile):** botão **"⋯"** que abre um **bottom sheet** com
   "Editar papel" / "Desativar" (não botões sempre visíveis).

## Mudanças por área

### 1. Navegação de abas
- **Desktop:** mantém a barra de pílulas atual (`hidden md:flex`).
- **Mobile (`md:hidden`):** grade `grid grid-cols-2 gap-2`. Cada seção é um card
  com ícone + rótulo; a ativa destacada (fundo branco + sombra, como a pílula
  ativa). Selo "Dono" e o contador de pendentes da Equipe entram como marcador
  discreto dentro do card.
- Mesmo estado Alpine `tab`; só muda a apresentação do seletor.

### 2. Aba Equipe — Membros
- **Desktop:** mantém a tabela (`hidden md:block` no wrapper da tabela).
- **Mobile (`md:hidden`):** lista de **cards**. Cada card: avatar (inicial, cor
  do papel), nome + e-mail, badges de papel e status, e um botão **"⋯"** no
  canto.
- **Bottom sheet de ações** (novo): ao tocar "⋯", abre sheet com o nome do
  membro e as ações "Editar papel" e "Desativar" (chamam `salvar('membro')`,
  como hoje). Fica dentro do mesmo `x-data`; backdrop `z-40`, sheet `z-50`.
  Estado: `sheetMembroId` (id do membro aberto, `null` = fechado). Membros com
  `papel === 'dono'` não mostram o "⋯" (igual à regra atual da tabela).

### 3. Aba Equipe — Aprovações pendentes
- Cada item hoje é uma linha `flex` (avatar + select + Aprovar + Rejeitar) que
  aperta no mobile. Tornar responsivo: no mobile o card empilha — dados no topo,
  select de papel em largura cheia, e Aprovar/Rejeitar lado a lado embaixo.
  No desktop mantém a linha atual. Mesmos handlers `aprovarMembro` /
  `rejeitarMembro`.

### 4. Aba Assinatura — Faturas
- **Desktop:** mantém a tabela.
- **Mobile (`md:hidden`):** cards — data + período à esquerda, valor + status à
  direita, e "Baixar PDF" (chama o `mostrarToast` atual) numa linha inferior.

### 5. Modal de upgrade (planos)
- Grid `grid-cols-3` → **`grid-cols-1 md:grid-cols-3`**; conteúdo rolável dentro
  do modal no mobile (`max-h` + `overflow-y-auto` no painel). Sem mudança no
  desktop.

### 6. Ajustes finos
- **Horário de funcionamento:** a linha (toggle + dia + 2 time inputs) reflowa no
  mobile para não espremer os inputs (ex.: dia em cima, horários embaixo, ou
  `flex-wrap`). Desktop inalterado.
- **Formulários** (Perfil, Segurança, Dados da oficina, Config OS, Portal): já
  usam `grid-cols-1 sm:grid-cols-N` e empilham bem — **sem alteração**.
- Botões "Salvar" continuam inline (sem barra fixa).

## Fora de escopo
- Qualquer mudança de lógica/persistência (segue mock).
- Barra de ações fixa / sticky save.
- Alterações no desktop além do necessário para o grid de planos.

## Riscos / cuidados
- O bottom sheet de ações do membro deve ficar **dentro do `x-data`
  `configPage()`** (regra do projeto: sheets no mesmo wrapper).
- Tabelas e nav ganham duas versões (mobile/desktop) do mesmo dado — manter as
  duas alimentadas pelo mesmo estado Alpine para não divergir.
- A grade 2×2 precisa acomodar rótulos + selo "Dono" + contador sem quebrar
  linha feia; testar com os 4 rótulos reais.
