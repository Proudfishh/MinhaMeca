# MinhaMeca — Clientes PF/PJ + Telas de Clientes — Design Spec

**Data:** 2026-06-17
**Stack:** Laravel 11 · PHP 8.2 · Blade · Alpine.js · Tailwind CSS v4
**Fase:** 1 — Frontend com dados mockados

---

## 1. Escopo

Esta spec cobre quatro entregas interligadas:

1. **MockClienteService** — adicionar suporte a PF/PJ nos dados mockados
2. **Nova OS** — toggle PF/PJ na etapa "Novo cliente" do wizard
3. **Lista de Clientes** (`/oficina/clientes`) — cards com busca e indicadores
4. **Detalhe do Cliente** (`/oficina/clientes/{id}`) — header + abas Dados / Veículos / Histórico de OS

---

## 2. Modelo de dados (Fase 1 — Mock)

### 2.1 Campos novos em `MockClienteService`

Cada cliente recebe dois campos adicionais:

| Campo | Tipo | Descrição |
|---|---|---|
| `tipo` | `'pf'` \| `'pj'` | Pessoa física ou jurídica |
| `cnpj` | `string\|null` | Preenchido apenas para PJ; PF mantém `cpf` |

Regra de exibição: se `tipo === 'pj'`, mostrar `cnpj`; senão mostrar `cpf`.

**Dados mockados atualizados — tenant 1:**

| id | nome | tipo | cpf / cnpj | telefone |
|---|---|---|---|---|
| 1 | Carlos Henrique Souza | pf | 123.456.789-00 | (11) 99999-1111 |
| 2 | Ana Paula Ferreira | pf | 234.567.890-11 | (11) 99999-2222 |
| 3 | Roberto Alves Lima | pf | 345.678.901-22 | (11) 99999-3333 |
| 5 | Auto Peças Ltda | pj | 12.345.678/0001-99 | (11) 3333-4444 |

> O id 4 continua para o tenant 2 (Fernanda Costa, sem alterações).

### 2.2 Enriquecimento no controller

`ClienteController` injeta `MockOsService` e `MockVeiculoService` para calcular, por cliente:
- `total_os` — contagem de OS via `MockOsService::byCliente($id)`
- `os_ativa` — primeira OS onde `etapa_atual !== 'finalizacao'` (ou `null`)
- `total_veiculos` — contagem via `MockVeiculoService::byCliente($id)`

---

## 3. Nova OS — suporte PJ na etapa "Novo cliente"

### 3.1 Toggle PF/PJ

Dentro do painel "Novo cliente" (mode `clienteMode === 'novo'`), antes dos campos:

```
[PF — Pessoa Física]  [PJ — Pessoa Jurídica]
```

Pills no mesmo estilo dos seletores existentes (`border-spark` quando ativo).

### 3.2 Campos por tipo

**PF (padrão atual — sem alteração funcional):**
- Nome completo `*`
- CPF `*`
- Telefone `*`
- E-mail (opcional)

**PJ (novo):**
- Razão Social `*`
- CNPJ `*`
- Telefone `*`
- E-mail removido do formulário mínimo (pode ser preenchido no detalhe depois)

### 3.3 Mudanças no Alpine (`novaOs()`)

```js
novoCliente: {
    tipo: 'pf',          // novo
    nome: '',
    cpf: '',
    cnpj: '',            // novo
    telefone: '',
    email: '',
},
```

`podeAvancar` no step 1, modo `novo`:
- PF: `nome` + `cpf` + `telefone` não vazios
- PJ: `nome` + `cnpj` + `telefone` não vazios

Hidden inputs adicionais no form:
```html
<input type="hidden" name="cliente_tipo"      :value="novoCliente.tipo">
<input type="hidden" name="cliente_cnpj_novo" :value="novoCliente.cnpj">
```

### 3.4 O que não muda

Modos "Já cadastrado" e "Sem cliente" — inalterados. Steps 2 e 3 — inalterados. `store()` — continua mockado.

---

## 4. Lista de Clientes (`/oficina/clientes`)

### 4.1 Controller

```php
// ClienteController::index()
$tenantId = session('tenant_id', 1);
$clientes = $this->clienteService->all($tenantId);
// enriquece com contagem de OS e OS ativa
return view('oficina.clientes.index', compact('clientes'));
```

### 4.2 Layout

**Header:**
```
Clientes  [badge contador]              [+ Novo Cliente — alert Fase 1]
```

**Barra de busca:**
- Input com ícone de lupa
- Alpine filtra em tempo real por: `nome`, `cpf`, `cnpj`, `telefone`
- Placeholder: "Buscar por nome, CPF, CNPJ ou telefone..."

**Grid de cards:**
- 1 coluna no mobile, 2 colunas no `lg:`
- Cada card:

```
┌──────────────────────────────────────────────────────────────┐
│  [Avatar]   Nome / Razão Social    [badge PF] ou [badge PJ]  │
│             CPF / CNPJ  ·  Telefone                          │
│             [badge OS ativa — cor da etapa]  ·  x OS total   │
└──────────────────────────────────────────────────────────────►│
```

- Avatar: círculo com inicial(is), `bg-ocean` para PF, `bg-spark` para PJ
- Badge PJ: pill pequeno (`bg-spark/10 text-spark`) visível apenas para PJ
- Badge OS ativa: aparece somente se `os_ativa !== null`, exibe etapa com cor correspondente
- Sem OS ativa: mostra "x OS" em texto `text-muted`
- Card inteiro é `<a href="route('oficina.clientes.show', $cliente['id'])">` com `hover:` sutil

**Empty state (busca sem resultado):**
```
[ícone usuário]
Nenhum cliente encontrado.
Tente buscar por outro nome, CPF ou telefone.
```

**Empty state (sem clientes cadastrados):**
```
[ícone usuário]
Nenhum cliente cadastrado ainda.
[+ Cadastrar primeiro cliente — alert]
```

### 4.3 Alpine

```js
// x-data na página
{
    busca: '',
    clientes: @json($clientes),   // via script tag, não no atributo
    get clientesFiltrados() {
        if (!this.busca.trim()) return this.clientes;
        const q = this.busca.toLowerCase();
        return this.clientes.filter(c =>
            c.nome.toLowerCase().includes(q) ||
            (c.cpf  && c.cpf.includes(q))   ||
            (c.cnpj && c.cnpj.includes(q))  ||
            c.telefone.includes(q)
        );
    }
}
```

> Mesmo padrão de embutir JSON no `<script>` adotado na correção da Nova OS.

---

## 5. Detalhe do Cliente (`/oficina/clientes/{id}`)

### 5.1 Controller

```php
// ClienteController::show($id)
$cliente  = $this->clienteService->find($id);
abort_if(!$cliente, 404);
$veiculos = $this->veiculoService->byCliente($id);
$osDoCliente = $this->osService->byCliente($id);
$etapas   = MockOsService::ETAPAS;
return view('oficina.clientes.show', compact('cliente', 'veiculos', 'osDoCliente', 'etapas'));
```

### 5.2 Header

```
[Avatar grande]   Nome / Razão Social   [badge PF/PJ]
                  Documento · Telefone · Email (se houver)
                                   [Nova OS →]  [Editar — alert]
```

- Avatar 48px, mesma lógica de cor (ocean PF / spark PJ)
- Breadcrumb: `Clientes` → nome do cliente

### 5.3 Abas

Controladas por Alpine `x-data="{ tab: 'dados' }"`. Três abas:

**Aba Dados**
- Grid de campos em modo leitura
- PF: Nome, CPF, Telefone, E-mail
- PJ: Razão Social, CNPJ, Telefone, E-mail
- Cada campo: label muted pequeno + valor em texto void
- Rodapé: "Edição disponível em breve" (Fase 1)

**Aba Veículos**
- Cards com: ícone veículo + placa (mono, destaque) + marca/modelo/ano/cor
- Se veículo tem OS ativa: badge colorido com etapa atual
- Botão "Adicionar veículo" → alert Fase 1
- Empty state se não houver veículos

**Aba Histórico de OS**
- Lista de linhas, ordenada por `data_entrada` decrescente
- Cada linha: ID OS (mono) + badge etapa + veículo resumido + data + valor total
- Linha é link para `/oficina/os/{id}`
- Empty state se sem histórico

### 5.4 Valor total na OS

`MockOsService` já possui campo `total` pré-calculado em cada OS (`'total' => 495.00`). Usar diretamente — sem recalcular. OS sem serviços têm `total => 0.00`.

A ordenação por `data_entrada` decrescente para a aba Histórico deve ser feita no controller via `usort`:

```php
usort($osDoCliente, fn($a, $b) => strcmp($b['data_entrada'], $a['data_entrada']));
```

---

## 6. Arquivos a criar / modificar

| Arquivo | Ação |
|---|---|
| `app/Services/Mock/MockClienteService.php` | Modificar — adicionar `tipo`, `cnpj`, cliente PJ |
| `app/Http/Controllers/Oficina/ClienteController.php` | Implementar `index()` e `show()` |
| `resources/views/oficina/clientes/index.blade.php` | Criar |
| `resources/views/oficina/clientes/show.blade.php` | Criar |
| `resources/views/oficina/os/create.blade.php` | Modificar — toggle PF/PJ no modo "novo cliente" |

Rotas já existem em `routes/oficina.php` — sem alteração necessária.

---

## 7. Restrições Fase 1

- Botão "Novo Cliente" na lista → `alert('Fase 1 — mockado')`
- Botão "Editar" no detalhe → `alert('Fase 1 — mockado')`
- Botão "Adicionar veículo" no detalhe → `alert('Fase 1 — mockado')`
- Não há paginação — todos os clientes do tenant são exibidos
- Não há ordenação interativa
- JSON dos dados sempre embutido no `<script>`, nunca no atributo `x-data`
