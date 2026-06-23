# Estoque Mobile — Design Spec
**Data:** 2026-06-23
**Escopo:** Remasterização completa da aba Estoque para mobile com gestão completa: peças, balanço e orçamentos.

---

## 1. Visão Geral

A tela de estoque mobile passa a ter **três abas** (Peças / Balanço / Orçamentos) com um **FAB contextual** que muda de função conforme a aba ativa. O layout de lista usa cards limpos com quantidade em destaque e indicador de status por cor.

---

## 2. Estrutura de Navegação

### 2.1 Header
- Título "Estoque" + badge total de itens (azul) + badge de alertas (amarelo, só aparece se > 0)
- Sem botões no header — todas as ações primárias ficam no FAB

### 2.2 Métricas compactas (acima das tabs)
Grid 2×1 visível no mobile:
- **Valor total em estoque** (R$ formatado)
- **Sem estoque** (vermelho se > 0, cinza se 0)

Os outros dois indicadores (Total de Itens, Estoque Baixo) ficam visíveis apenas no desktop (grid 4 colunas existente).

### 2.3 Tabs
Três tabs em segmented control (pill): **Peças · Balanço · Orçamentos**

### 2.4 FAB (Floating Action Button)
Botão azul `+` fixo no canto inferior direito. Ação contextual por aba:
- **Aba Peças** → abre bottom sheet "Cadastrar nova peça"
- **Aba Balanço** → inicia nova contagem física
- **Aba Orçamentos** → abre fluxo "Novo orçamento"

---

## 3. Aba Peças

### 3.1 Busca e filtros
- Campo de busca por nome da peça
- Pills de categoria com scroll horizontal: `Todos` + categorias criadas pelo usuário
- Filtro de status (`Todos / Baixo / Sem estoque`) — pills secundárias abaixo das categorias

### 3.2 Card de peça
Layout (da esquerda para direita):
```
[Nome da peça]          [QTY]  [●]  [›]
[Categoria · R$ xx/un]
```
- Quantidade em fonte mono grande; cor: verde (ok), âmbar (baixo), vermelho (zerado)
- Ponto colorido de status
- Chevron `›` à direita — indica que é clicável
- Background do card levemente tingido quando status é baixo ou zerado

### 3.3 Bottom sheet — Detalhe / Especificações (toque no card)
Abre ao tocar em qualquer card. Conteúdo:

**Cabeçalho:** nome da peça + categoria + ponto de status

**Grid de specs (2×2):**
| Campo | Detalhe |
|---|---|
| Custo | Valor pago ao fornecedor |
| Venda | Preço de venda ao cliente |
| Margem | Calculado automaticamente `((venda - custo) / custo * 100)%` exibido em verde |
| Em estoque | Quantidade atual |

**Localização:** linha com ícone de pin + texto livre ("Prateleira A3 — Corredor 2")

**Ações:**
- `↓ Entrada` — botão outline escuro
- `↑ Saída` — botão sólido escuro

---

### 3.4 Fluxo de Entrada de Mercadoria
1. Bottom sheet: informa quantidade recebida (botões +/−)
2. Campo opcional: fornecedor e número de nota fiscal
3. Botão "Confirmar entrada" → atualiza estoque e registra no histórico de balanço

---

### 3.5 Fluxo de Saída

**Passo 1 — Quantidade**
- Botões +/− para informar qtd saindo
- Validação: não permite qtd > estoque atual

**Passo 2 — Destino** (escolha exclusiva):
- **Vincular a OS existente** → lista OS com status não-finalizado (abertas ou em andamento) para selecionar; peça é associada à OS escolhida
- **Criar nova OS (venda avulsa)** → avança para Passo 3

**Passo 3 — Cliente (apenas venda avulsa)**
- Campo de busca entre clientes cadastrados
- Lista de resultados clicável
- Separador "ou"
- Botão `+ Cadastrar novo cliente` (abre mini-form: nome, telefone)
- Botão final: **"Criar OS e registrar saída"** → cria OS, registra saída, fecha bottom sheet

---

### 3.6 Bottom sheet — Cadastrar nova peça (FAB)
Campos:
- Descrição (nome da peça)
- Categoria (select com opção "Nova categoria…" que abre campo de texto)
- Custo unitário
- Preço de venda (margem calculada em tempo real)
- Estoque mínimo
- Localização no estoque (texto livre)
- Quantidade inicial

---

## 4. Aba Balanço

### 4.1 Sub-tabs
Duas sub-tabs dentro da aba: **Contagem Física · Histórico**

### 4.2 Contagem Física
- Instrução contextual: "Informe a quantidade real de cada item na prateleira"
- Lista de todas as peças com:
  - Nome + quantidade atual no sistema
  - Botões +/− para informar quantidade real contada
  - Divergências destacadas: quando qtd real ≠ qtd sistema → linha com fundo âmbar + texto "X divergência(s)"
- Botão fixo no rodapé: **"Confirmar Balanço"** → salva contagem, atualiza quantidades e registra evento no histórico

### 4.3 Histórico de Movimentações
Lista cronológica reversa de eventos:
- Entrada de mercadoria (verde, `↓`)
- Saída para OS (azul, `↑`)
- Saída venda avulsa (azul, `↑`)
- Ajuste de balanço (âmbar, `⚖`)

Cada linha: ícone + nome da peça + qtd (+/−) + data + origem (OS-xxx ou "Balanço")

---

## 5. Aba Orçamentos

### 5.1 Lista de orçamentos
Card por orçamento:
- Código (ORC-001, ORC-002…)
- Valor total (mono, destaque)
- Badge de status: `Vinculado · OS-xxx` (azul) ou `Avulso · PDF` (cinza)
- Data de criação

### 5.2 Fluxo de novo orçamento (FAB)
1. **Seleção de peças** — lista de peças do estoque com campo de busca; toque adiciona ao orçamento com qtd editável
2. **Resumo** — lista itens selecionados + total calculado
3. **Destino:**
   - `Vincular a OS` → busca OS não-finalizadas (abertas ou em andamento)
   - `Salvar como PDF` → gera PDF avulso para download/compartilhamento

---

## 6. Categorias

- Criadas pelo usuário (sem categorias predefinidas pelo sistema)
- Gerenciadas dentro do bottom sheet de cadastro de peça: campo "Categoria" com opção "Nova categoria…"
- Pills de categoria na aba Peças carregam as categorias existentes dinamicamente

---

## 7. Desktop (sem alteração de estrutura)

A tabela desktop existente é preservada. As novas features (Balanço e Orçamentos) são expostas como tabs também no desktop, mantendo a mesma lógica mas em layout de tabela onde aplicável.

---

## 8. Dados (extensão do mock)

Campos a adicionar em `MockEstoqueService`:
```php
'custo'        => 52.00,   // valor de compra
'localizacao'  => 'Prateleira A3',
'categoria'    => 'Freios',
```

Novos mocks necessários:
- `MockBalancoService` — histórico de movimentações
- `MockOrcamentoEstoqueService` — orçamentos de peças

---

## 9. Fora de escopo

- Integração real com fornecedores / NF-e
- Geração real de PDF (mock retorna mensagem de sucesso)
- Múltiplos depósitos / filiais
