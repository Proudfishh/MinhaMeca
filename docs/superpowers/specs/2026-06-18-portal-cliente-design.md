# Portal do Cliente — Design Spec

**Data:** 2026-06-18
**Fase:** 1 (frontend mockado)
**Abordagem escolhida:** A — Portal de página única

---

## 1. Visão geral

Área autenticada para clientes (donos de veículos) acompanharem o status das OSes em andamento e consultarem o histórico de serviços. Separada do portal da oficina em rota, middleware, layout e sessão.

O cliente acessa via tab "Cliente" na tela de login existente, autentica com CPF + email, e cai numa página única com OSes ativas no topo e histórico expansível embaixo.

---

## 2. Fluxo de autenticação

### Login (`GET /login`, tab "Cliente")
O bloco cliente da `auth/login.blade.php` ganha as mesmas três sub-abas já presentes no bloco oficina:

| Sub-aba | Campos | Action |
|---|---|---|
| Entrar | CPF + email | `POST /login/cliente` |
| Criar conta | Nome, CPF, email, senha | `POST #` (stub Fase 1) |
| Esqueci a senha | CPF + email | `POST #` (stub Fase 1) |

### Pós-login
- **1 oficina vinculada** → seta sessão e redireciona direto para `cliente.veiculos.index`
- **N oficinas vinculadas** → redireciona para `auth/selecionar-oficina.blade.php`

### Seleção de oficina (`resources/views/auth/selecionar-oficina.blade.php`)
Tela no mesmo estilo dark do login. Exibe "Olá, [nome]" + lista de cards clicáveis, um por oficina. Cada card é um `<form POST /login/cliente/selecionar>` com `tenant_id` hidden. Ao selecionar: seta `cliente_auth`, `cliente_id`, `cliente_nome`, `tenant_id` na sessão e redireciona para `cliente.veiculos.index`.

### Logout
`POST /cliente/logout` — limpa todas as chaves de sessão do cliente e redireciona para `/login`.

### Sessão do cliente
| Chave | Valor |
|---|---|
| `cliente_auth` | `true` |
| `cliente_id` | ID do cliente |
| `cliente_nome` | Nome completo |
| `tenant_id` | ID da oficina selecionada |

---

## 3. Layout do portal (`layouts/cliente.blade.php`)

Header fixo branco:
- Esquerda: logo "MinhaMeca" + nome da oficina (em texto secundário pequeno)
- Direita: "Olá, [nome do cliente]" + botão "Sair" (`POST /cliente/logout`)

`<main>` com `bg-surface`, padding `p-6`, scroll livre. Sem sidebar.

---

## 4. Página principal (`cliente/veiculos/index.blade.php`)

Rota: `GET /cliente/veiculos` → `Cliente\VeiculoController::index()`

### Controller (`Cliente\VeiculoController::index()`)
```php
$clienteId = session('cliente_id');
$todasOs   = $osService->byCliente($clienteId);
// OSes com data_entrega_real preenchida = concluídas (histórico)
$ativas    = collect($todasOs)->whereNull('data_entrega_real')->values()->all();
$historico = collect($todasOs)->whereNotNull('data_entrega_real')->values()->all();
return view('cliente.veiculos.index', compact('ativas', 'historico'));
```

**Nota:** `MockOsService` não possui etapa `concluida`. O critério de separação usa o campo `data_entrega_real` (null = em andamento, preenchido = concluída e entregue). Pelo menos uma OS no mock deve ter `data_entrega_real` para que o histórico seja demonstrável.

### Bloco "Em andamento"
Um card por OS ativa. Estrutura de cada card:

1. **Cabeçalho**: placa/modelo do veículo + badge colorido da etapa atual
2. **Barra de progresso**: 6 etapas em sequência (checkin → diagnóstico → peças → serviço → testes → finalização). Etapas concluídas: preenchidas. Etapa atual: destacada com anel. Futuras: vazias.
3. **Previsão de entrega**: ícone de calendário + data formatada
4. **Descrição do cliente**: texto informado na abertura da OS
5. **Lista de serviços**: ícone de status (✓ concluído / ⚙ em andamento / ○ pendente) + descrição + valor alinhado à direita
6. **Rodapé**: valor total estimado

Estado vazio (sem OSes ativas): card com ícone + "Nenhum veículo em manutenção no momento."

### Bloco "Histórico"
Lista de cards compactos, collapsed por padrão (Alpine `x-data="{ aberto: false }"`).

**Collapsed**: data de entrada, veículo, número de serviços, valor total + chevron.

**Expanded** (ao clicar): lista completa de serviços com status, data de entrega real, mecânico responsável.

---

## 5. Arquivos

### Modificados
| Arquivo | Mudança |
|---|---|
| `resources/views/auth/login.blade.php` | Sub-abas "Criar conta" e "Esqueci a senha" no bloco cliente |
| `routes/web.php` | `POST /login/cliente/selecionar` + `POST /cliente/logout` |
| `app/Http/Controllers/Auth/ClienteAuthController.php` | Método `selecionarOficina()` |
| `app/Services/Mock/MockOsService.php` | Adicionar `data_entrega_real` nas OSes encerradas (mínimo 1 para demo do histórico) |

### Criados
| Arquivo | Propósito |
|---|---|
| `resources/views/auth/selecionar-oficina.blade.php` | Tela de seleção de oficina (multi-tenant) |
| `resources/views/components/layouts/cliente.blade.php` | Layout do portal cliente |
| `resources/views/cliente/veiculos/index.blade.php` | Página principal (OSes ativas + histórico) |
| `app/Http/Controllers/Cliente/VeiculoController.php` | `index()` com separação ativas/histórico |

### Fora de escopo (Fase 1)
- `Cliente\OsController` — detalhe individual de OS não necessário (cards expansíveis cobrem o caso)
- `Cliente\HistoricoController` — histórico está na página principal
- Lógica real de criar conta e recuperação de senha
