# Módulo de Configurações — Design Spec

**Data:** 2026-06-18
**Fase:** 1 (frontend mockado)
**Abordagem escolhida:** Abas horizontais Alpine, sem RBAC real

---

## 1. Visão geral

Página `/oficina/configuracoes` com 4 abas horizontais: Minha Conta, Plataforma, Equipe e Assinatura. Todas as ações são client-side (Alpine) — nenhum POST real na Fase 1. Salvar qualquer formulário exibe toast "Configurações salvas!". Abas Plataforma, Equipe e Assinatura exibem badge "Dono" (visual, sem restrição de acesso nesta fase). RBAC real será implementado em fase futura.

---

## 2. Arquitetura

### Controller (`ConfiguracoesController::index()`)
Passa para a view:
- `$config` — array mock com dados da oficina, equipe, assinatura e preferências
- `$tabAtiva` — derivada de `request()->query('tab', 'conta')`

### View (`resources/views/oficina/configuracoes/index.blade.php`)
Usa `<x-layouts.oficina title="Configurações">`. Alpine component `configPage()` carrega dados de `window.__config`. Navegação entre abas via `x-data="{ tab: '{{ $tabAtiva }}' }"`. Cada aba é um painel `x-show="tab === '...' "`.

### Mock Service (`MockConfiguracaoService.php`)
Retorna estrutura com:
```php
[
  'oficina'   => [...],  // dados da oficina
  'horario'   => [...],  // por dia da semana
  'os_config' => [...],  // prazo garantia, prefixo OS
  'portal'    => [...],  // mensagem boas-vindas, toggles
  'equipe'    => [...],  // membros + pendentes
  'assinatura'=> [...],  // plano, faturas
]
```

---

## 3. Aba "Minha Conta"

### Bloco Perfil
- Avatar circular + botão "Alterar foto" (placeholder)
- Campos: nome completo (editável), e-mail (readonly + badge "verificado"), telefone (editável)
- Botão "Salvar perfil" → toast

### Bloco Segurança
- Campos: senha atual, nova senha, confirmar nova senha
- Card "Sessões ativas": dispositivo mock ("Chrome · Windows · São Paulo") + botão "Encerrar"
- Toggle 2FA desabilitado com label "Autenticação em dois fatores — em breve"
- Botão "Alterar senha" → toast

### Bloco Notificações
Toggles Alpine (`x-model`) para cada evento:
- OS concluída e pronta para retirada
- Garantia vencendo em 10 dias
- Estoque abaixo do mínimo
- Pendência financeira vencida
- Novo cadastro de funcionário aguardando aprovação

Botão "Salvar preferências" → toast

### Bloco Aparência
- Toggle "Tema escuro" (visual, sem efeito real na Fase 1)
- Seletor de idioma: "Português (BR)" ativo; demais com badge "Em breve"

---

## 4. Aba "Plataforma" (badge "Dono")

### Bloco Dados da oficina
- Logo atual + botão "Alterar logo" (placeholder)
- Campos: nome da oficina, CNPJ (formatado), logradouro, número, bairro, cidade, UF, CEP, telefone, e-mail de contato, site
- Botão "Salvar dados" → toast

### Bloco Horário de funcionamento
- Tabela 7 linhas (Seg–Dom): toggle ativo/inativo + inputs `type="time"` abertura e fechamento
- Linhas desativadas ficam esmaecidas
- Botão "Salvar horário" → toast

### Bloco Ordens de Serviço
- Campo numérico "Prazo padrão de garantia (dias)" — valor default 90
  - Descrição auxiliar: "Aplicado automaticamente a todas as OS finalizadas"
- Campo "Prefixo do número de OS" (ex: `OS-2026-`)
- Botão "Salvar configurações de OS" → toast

### Bloco Portal do Cliente
- Textarea "Mensagem de boas-vindas" exibida após login do cliente
- Toggle "Exibir previsão de entrega para o cliente"
- Toggle "Exibir lista de serviços para o cliente"
- Botão "Salvar configurações do portal" → toast

---

## 5. Aba "Equipe" (badge "Dono")

### Bloco Aprovações pendentes
- Visível apenas quando `pendentes.length > 0` — badge numérico vermelho na aba
- Cards compactos: nome, e-mail, data do cadastro
- Cada card: select de papel + botão "Aprovar" (verde) + botão "Rejeitar" (vermelho)
- Aprovar: membro move para lista de membros ativos + toast
- Rejeitar: remove da lista + toast
- Mock: 1 aprovação pendente por padrão

### Bloco Membros da equipe
Tabela: avatar/inicial, nome, e-mail, papel (badge colorido), status (Ativo/Inativo), ações (editar papel, desativar)

Papéis disponíveis e cores:
| Papel | Cor badge |
|---|---|
| Dono | Violeta |
| Gerente | Azul |
| Mecânico | Âmbar |
| Recepção | Ciano |
| Financeiro | Verde |
| Vendedor | Rosa |

Rodapé: botão "Convidar funcionário" → modal com campo e-mail + select papel → "Convite enviado!" (mock)

Mock: 4 membros ativos cobrindo papéis diferentes

---

## 6. Aba "Assinatura" (badge "Dono")

### Bloco Plano atual
- Card com nome do plano ("Profissional"), badge "Ativo", recursos incluídos, data renovação, valor mensal
- Botão "Fazer upgrade" → modal com 3 colunas (Básico / Profissional / Enterprise), todos com badge "Em breve" exceto plano atual

### Bloco Método de pagamento
- Card: ícone cartão + últimos 4 dígitos mock ("•••• 4242") + validade + bandeira
- Botões "Alterar cartão" e "Remover" → toast "Funcionalidade disponível em breve"
- Texto auxiliar: "Gerenciado com segurança via gateway de pagamento"

### Bloco Histórico de faturas
- Tabela: 3 faturas mock (data, período, valor, status "Pago", botão "Baixar PDF" — placeholder)
- Rodapé: "Faturas geradas automaticamente a cada ciclo de cobrança"

---

## 7. Arquivos

### Criados
| Arquivo | Propósito |
|---|---|
| `app/Services/Mock/MockConfiguracaoService.php` | Dados mock para todas as seções |
| `resources/views/oficina/configuracoes/index.blade.php` | View principal com 4 abas |

### Modificados
| Arquivo | Mudança |
|---|---|
| `app/Http/Controllers/Oficina/ConfiguracoesController.php` | Implementar `index()` |

### Fora de escopo (Fase 1)
- RBAC real (restrição de acesso por papel)
- POST/persistência real de qualquer configuração
- Envio real de convites por e-mail
- Integração com gateway de pagamento
- Tema escuro funcional
