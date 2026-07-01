# Swipe-to-delete na listagem de orçamentos

**Data:** 2026-07-01
**Módulo:** Oficina › Orçamentos › Index
**Arquivo alvo:** `resources/views/oficina/orcamentos/index.blade.php`

## Objetivo

Permitir excluir um orçamento direto da listagem **mobile**, sem abrir o
detalhe: o usuário desliza o card para a esquerda, um botão vermelho de
"Excluir" é revelado, e ao tocá-lo aparece um bottom sheet de confirmação.

## Escopo

- **Somente mobile** (gesto de toque, cards). O desktop (tabela) permanece
  inalterado — exclusão continua acontecendo dentro do `show`.
- Abordagem escolhida: **Alpine + touch events** (sem dependência nova),
  coerente com o padrão do projeto (Alpine inline, mínimo de libs).

## Decisões confirmadas com o usuário

1. **Confirmação:** tocar no botão vermelho abre um **bottom sheet de
   confirmação** ("Excluir ORÇ-XXX?"), mesmo padrão visual do `show`. Só exclui
   após confirmar (2 toques — modelo mais seguro).
2. **Plataforma:** somente mobile (swipe). Desktop intacto.
3. **Persistência:** remoção **client-side** (o backend `update()` é mock e não
   persiste). Sem reload de página.
4. **Feedback:** toast "Orçamento excluído" após a remoção.

## Estrutura visual do card

Cada item da lista mobile vira um trilho com duas camadas:

- **Fundo (atrás):** faixa vermelha à direita, largura fixa (~88px), com ícone
  de lixeira + rótulo "Excluir". Sempre presente atrás do card.
- **Frente:** o card atual (o `<a>`), que desliza sobre o fundo via
  `translateX`.

```
Repouso:        [========== card =========]
Deslizando:  ←  [==== card ====][ 🗑 Excluir ]
```

O contêiner de cada item precisa de `position:relative; overflow:hidden` e o
fundo vermelho `position:absolute; inset-y-0; right-0`.

## Comportamento do gesto

Estado por card mantido no Alpine (mapa `id → offset` ou `aberto`).

- **`touchstart`:** guarda X/Y inicial e o offset atual do card.
- **`touchmove`:**
  - Calcula `dx` e `dy`. Se `|dx| > |dy|` e o movimento é para a **esquerda**,
    trata como swipe: `preventDefault()` no scroll e acompanha o dedo
    (`translateX` limitado ao intervalo `[-88, 0]`).
  - Se o movimento é predominantemente **vertical**, ignora → a lista rola
    normalmente.
- **`touchend`:**
  - Se ultrapassou o **threshold (~40px)**, trava **aberto** (`translateX =
    -88`, botão revelado).
  - Senão, volta para `0` (fechado). Transição suave em ambos.
- **Tap com card fechado:** navega para o orçamento (comportamento atual
  preservado — o card continua sendo/an­cora `<a>`).
- **Tap com card aberto:** o primeiro toque **apenas fecha** o card (não
  navega), evitando abertura acidental.
- **Um card aberto por vez:** abrir um fecha o anterior.
- **Fechar também:** rolar a lista ou tocar fora fecham o card aberto.

## Exclusão

1. Toque no botão vermelho → abre bottom sheet `Excluir ORÇ-XXX?` com botões
   **Cancelar** / **Excluir** (reaproveita o visual do sheet de exclusão do
   `show`: ícone de lixeira em círculo vermelho, texto "Esta ação não pode ser
   desfeita.").
2. **Cancelar:** fecha o sheet, card volta ao repouso.
3. **Excluir:**
   - Remove o item do array `orcamentos` do Alpine (client-side).
   - Anima a saída do card (colapso de altura + fade).
   - Métricas do topo (`filtrados`, pendentes, aprovados, valor) atualizam
     sozinhas — já são reativas a `filtrados`.
   - Mostra toast "Orçamento excluído" (~3s, auto-dismiss).

## Implementação (Alpine, dentro de `orcamentosIndex()`)

Novos campos/métodos, todos no componente existente:

- `swipeId` / `swipeAberto` — qual card está aberto (id) e seu offset.
- `swipeStartX`, `swipeStartY`, `swipeDx` — estado do gesto em andamento.
- `sheetExcluirId` — id do orçamento pendente de confirmação (`null` = fechado).
- `onTouchStart(orc, e)`, `onTouchMove(orc, e)`, `onTouchEnd(orc, e)`.
- `abrirSheetExcluir(id)` / `fecharSheetExcluir()`.
- `confirmarExcluir()` — remove do array, dispara toast, fecha sheet.
- `toastMsg` / `mostrarToast(msg)` — estado do toast.
- `offsetDe(id)` — retorna o `translateX` a aplicar via `:style` no card.

O bottom sheet de confirmação e o toast ficam **dentro do mesmo `x-data`**
(regra do projeto: sheets sempre no mesmo wrapper; backdrop `z-40`, sheet
`z-50`).

## Fora de escopo

- Persistência real no backend (fica TODO: quando existir rota de exclusão,
  chamar antes/depois da remoção client-side).
- Swipe/ação rápida no desktop.
- Outras ações por swipe (ex.: aprovar, editar) — só exclusão por ora.

## Riscos / cuidados

- **Tap × scroll × swipe:** a distinção por eixo dominante + threshold é o ponto
  mais delicado. Testar em toque real (Playwright/emulação) para não bloquear o
  scroll vertical nem disparar navegação acidental.
- **`overflow-hidden` do contêiner atual:** a lista mobile hoje é um único
  bloco com `rounded-2xl overflow-hidden`. O clip do swipe precisa ser por
  **item**, não pelo bloco todo, senão o botão vermelho vaza/borda arredondada
  quebra. Ajustar o wrapper de cada `x-for`.
- **Preservar o `<a>`:** manter navegação por tap sem `@click` que engula o
  gesto; usar handlers de touch que só cancelam a navegação quando houve swipe.
