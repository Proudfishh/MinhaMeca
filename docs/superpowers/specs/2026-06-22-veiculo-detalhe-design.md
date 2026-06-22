# Spec — Veículos Detalhe (`/oficina/veiculos/{id}`)

**Data:** 2026-06-22  
**Status:** Aprovado

---

## Objetivo

Aprofundar a tela de detalhe de veículo, que atualmente exibe apenas um header básico e uma tabela simples de OS. Os dois casos de uso principais são: revisão do histórico completo do veículo e manutenção/edição do cadastro.

---

## Layout — Opção B: Header fixo + abas

### 1. Header

- Ícone de carro (outline, 40×40px) com fundo `ocean/10` e borda `ocean/15`
- **Marca Modelo** em Syne bold (18px), `void`
- **ANO · Cor** em DM Sans (13px), `muted` — linha secundária
- Placa em JetBrains Mono com fundo `#0F172A0A` e borda `border` (igual ao padrão das OS)
- Link do proprietário em `spark` → `/oficina/clientes/{cliente_id}`
- Botão **Editar** (azul sólido, `spark`, sm) alinhado à direita do header

### 2. Stats (4 cards inline)

Abaixo do header, em linha (grid 4 colunas no desktop, 2×2 no mobile):

| Campo | Valor | Formato |
|-------|-------|---------|
| KM Atual | `$veiculo['km']` | JetBrains Mono |
| Total OS | `count($osDoVeiculo)` | Mono, inteiro |
| Total Gasto | soma dos `valor_total` das OS | `R$ X.XXX` mono |
| Última OS | `data_entrada` da OS mais recente | `d/m/Y` |

### 3. Abas

Implementadas com Alpine.js (`x-data="{ tab: 'historico' }"`).

#### Aba Histórico (padrão)

Lista de OS do veículo, ordenada por `data_entrada` desc:

Cada linha:
- ID da OS em mono (`#94A3B8`, tamanho xs)
- Título da OS (descrição do serviço)
- Badge da etapa (cor conforme design tokens de stages)
- Valor total em mono (`R$ X.XXX`)
- Data de entrada (`d/m/Y`)
- Linha clicável → `href="/oficina/os/{id}"`
- Hover: `bg-slate-50`

Se não houver OS: estado vazio com mensagem "Nenhuma OS registrada para este veículo."

#### Aba Dados

Grid 2 colunas com todos os campos do veículo:

| Campo | Valor |
|-------|-------|
| Marca | `$veiculo['marca']` |
| Modelo | `$veiculo['modelo']` |
| Ano | `$veiculo['ano']` |
| Cor | `$veiculo['cor']` |
| Placa | mono |
| Chassi | mono (campo novo no mock) |
| Combustível | texto |
| Câmbio | texto |
| KM Atual | mono |
| Proprietário | link para cliente |

Cada campo: label pequeno (uppercase, muted, 11px) + valor abaixo.  
Botão "Editar" no topo direito desta aba também.

---

## Modal Editar

- Título: "Editar Veículo"
- Overlay backdrop com `x-show` + `x-transition`
- Fechar: botão ×, clique no backdrop, tecla Escape

**Campos editáveis no modal:**

| Campo | Tipo | Placeholder |
|-------|------|-------------|
| KM Atual | input number | ex: 87400 |
| Cor | input text | ex: Prata |
| Placa | input text (uppercase) | ex: ABC-1234 |
| Chassi | input text | ex: 9BW... |
| Combustível | select | Flex / Gasolina / Diesel / Elétrico / Híbrido |
| Câmbio | select | Manual / Automático / CVT |

**Campos não editáveis no modal:** Marca, Modelo, Ano (alteração de veículo é operação separada — fora do escopo da Fase 1).

**Ação Salvar:** `alert('Fase 1 — funcionalidade mockada')` + fecha o modal.

---

## Dados Mock — alterações necessárias

### `app/Services/Mock/MockVeiculoService.php`

Adicionar campos a cada veículo:

| Campo | Tipo | Exemplo |
|-------|------|---------|
| `km` | int | `87400` |
| `chassi` | string | `'9BWZZZ377VT004251'` |
| `combustivel` | string | `'Flex'` |
| `cambio` | string | `'Automático'` |

### `app/Http/Controllers/Oficina/VeiculoController.php`

Método `show()` — calcular e passar ao template:

```php
$totalGasto = collect($osDoVeiculo)->sum('valor_total');
$ultimaOS   = collect($osDoVeiculo)->first(); // já ordenado desc
```

Passar: `compact('veiculo', 'cliente', 'osDoVeiculo', 'etapas', 'totalGasto', 'ultimaOS')`

---

## Responsivo

- **Desktop**: header 2 colunas (info + botão), stats 4 colunas, abas normais
- **Mobile**: header empilhado, stats 2×2, abas com scroll horizontal se necessário

---

## Fora do escopo (Fase 1)

- Upload de foto do veículo
- Aba "Docs" (documentos, CRLV, seguro)
- Histórico de KM por visita
- Troca de proprietário
