# Nova OS — Design Spec

**Data:** 2026-06-16
**Stack:** Laravel 11 · Blade · Alpine.js · Tailwind CSS v4
**Fase:** 1 — Frontend com dados mockados

---

## 1. Visão Geral

Tela de abertura de nova Ordem de Serviço. Fluxo wizard em 3 etapas:
**Cliente → Veículo → Problema**

A OS é criada ao final do fluxo e nasce na etapa "Check-in". O check-in com fotos do veículo acontece separadamente na tela de detalhe da OS.

---

## 2. Rota e Arquivos

| Item | Valor |
|---|---|
| Rota | `GET /oficina/os/nova` |
| Controller | `App\Http\Controllers\Oficina\OsController@create` (stub existente) |
| View | `resources/views/oficina/os/create.blade.php` |
| Mock data | `MockClienteService`, `MockVeiculoService`, `MockOsService` |
| Tecnologia | Blade + Alpine.js — sem Livewire |

Ao submeter, a rota `POST /oficina/os` cria a OS mockada e redireciona para `/oficina/os/{id}`.

---

## 3. Layout Geral

- Usa o layout `components/layouts/oficina.blade.php`
- Card centralizado com `max-w-2xl` no desktop
- No mobile: card ocupa tela inteira, sem margens laterais, padding interno generoso para toque
- Conteúdo da etapa é scrollável; botões de navegação nunca somem da tela

---

## 4. Stepper

**Desktop:** 3 círculos numerados com linha conectora horizontal.

```
(1) Cliente ——————— (2) Veículo ——————— (3) Problema
```

- Etapa concluída: círculo preenchido com cor `spark` + ícone de check
- Etapa atual: círculo com borda `spark` e número em destaque
- Etapa futura: círculo cinza `muted`

**Mobile:** indicador compacto no topo do card:

```
Etapa 1 de 3 · Cliente
[████████░░░░░░░░░░░░]  ← barra de progresso fina (33%)
```

---

## 5. Navegação entre Etapas

- Botões **Voltar** e **Avançar** no rodapé do card
- No mobile: fixos no rodapé da tela (`position: fixed; bottom: 0`)
- "Avançar" fica desabilitado (opacidade reduzida, não clicável) enquanto nenhuma opção da etapa está selecionada
- Exceção: etapas com "Sem cliente" / "Sem veículo" selecionados já habilitam o avanço
- Na etapa 3, o botão "Avançar" é substituído por **"Abrir OS"** (cor `spark`, destaque visual)

---

## 6. Etapa 1 · Cliente

### Seletor de modo

Três pill-buttons no topo da etapa (radio-style):

```
[ Já cadastrado ]   [ Novo cliente ]   [ Sem cliente ]
```

Estado padrão: nenhum selecionado (botão "Avançar" desabilitado).

### Modo: Já cadastrado

- Campo de busca com ícone de lupa
- Filtra clientes mockados em tempo real conforme o usuário digita (nome, CPF ou telefone)
- Resultados exibidos como lista de cards compactos:
  ```
  ● João Silva          CPF 123.456.789-00    (11) 99999-0000
  ```
- Card selecionado: borda `spark` + ícone de check no canto superior direito
- Apenas um cliente pode ser selecionado por vez

### Modo: Novo cliente

Formulário inline com os campos:

| Campo | Tipo | Obrigatório |
|---|---|---|
| Nome completo | text | sim |
| CPF | text (máscara) | sim |
| Telefone | text (máscara) | sim |
| E-mail | email | não |

- Layout: coluna única no mobile, dois por linha no desktop
- Validação: apenas visual na Fase 1 (campos marcados com borda vermelha se vazios ao avançar)

### Modo: Sem cliente

Mensagem discreta:

> "A OS será aberta sem cliente vinculado. Você pode vincular um cliente posteriormente na tela de detalhe."

Ícone informativo + texto em `muted`. Botão "Avançar" habilitado automaticamente.

---

## 7. Etapa 2 · Veículo

### Seletor de modo

```
[ Veículo existente ]   [ Novo veículo ]   [ Sem veículo ]
```

Estado padrão: nenhum selecionado.

### Modo: Veículo existente

- **Se cliente selecionado na etapa anterior:** lista automaticamente os veículos do cliente como cards, sem campo de busca.
  ```
  🚗  ABC-1234   Honda Civic 2020   Prata
  ```
- **Se veio sem cliente:** exibe campo de busca por placa ou modelo, filtra veículos mockados.
- Seleção com mesma UX da etapa de cliente (borda spark + check).

### Modo: Novo veículo

Formulário inline:

| Campo | Tipo | Obrigatório |
|---|---|---|
| Placa | text (máscara ABC-1234 / BRA2E19) | sim |
| Marca | text | sim |
| Modelo | text | sim |
| Ano | number (4 dígitos) | sim |
| Cor | text | não |

- Layout: coluna única no mobile, dois por linha no desktop

### Modo: Sem veículo

> "A OS será aberta sem veículo vinculado. Você pode vincular posteriormente."

Botão "Avançar" habilitado automaticamente.

---

## 8. Etapa 3 · Problema

### Mini-resumo no topo

Chips compactos mostrando o que foi selecionado nas etapas anteriores:

```
👤 João Silva   🚗 ABC-1234 · Civic 2020
```

Se etapa anterior veio vazia, chip mostra "Sem cliente" / "Sem veículo" em `muted`.

### Campos

| Campo | Tipo | Obrigatório |
|---|---|---|
| Descrição do problema | textarea (mín. 4 linhas) | sim |
| Mecânico responsável | select (lista mockada) | não |

- Placeholder do textarea: *"Ex: Cliente relata barulho ao frear no lado dianteiro esquerdo ao reduzir velocidade acima de 80km/h."*
- Mecânico pode ficar em branco — atribuição posterior na tela de detalhe

### Botão "Abrir OS"

- Substitui "Avançar"
- Cor `spark`, texto branco, largura total no mobile
- Desabilitado se a descrição estiver vazia
- Na Fase 1: cria OS mockada em `MockOsService`, redireciona para `/oficina/os/{id}` com mensagem de sucesso

---

## 9. Responsividade

| Breakpoint | Comportamento |
|---|---|
| Mobile (`< 640px`) | Card full-width, sem margens laterais; botões fixos no rodapé; stepper compacto com barra de progresso; formulários em coluna única |
| Tablet (`640px–1024px`) | Card com margens modestas; botões no rodapé do card; stepper completo |
| Desktop (`> 1024px`) | Card centralizado `max-w-2xl`; stepper completo; formulários em duas colunas |

---

## 10. Estados da Fase 1

- Busca de clientes: filtra array mockado de `MockClienteService` via Alpine (sem requisição ao servidor)
- Busca de veículos: filtra array mockado de `MockVeiculoService` via Alpine
- Mecânicos: array fixo mockado inline na view
- Submit: `OsController@store` adiciona OS ao array mockado e redireciona para o detalhe com `session()->flash('success', 'OS aberta com sucesso')`

---

## 11. Fora do Escopo — Fase 1

- Upload de fotos (pertence ao Check-in, na tela de detalhe)
- Prioridade da OS (configurável depois no detalhe)
- Previsão de entrega (configurável depois no detalhe)
- Validações reais de CPF e placa
- Persistência real em banco de dados
