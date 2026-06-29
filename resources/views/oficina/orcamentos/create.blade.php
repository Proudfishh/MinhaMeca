@php $orcamento = $orcamento ?? null; @endphp
<x-layouts.oficina :title="$orcamento ? 'Editar Orçamento' : 'Novo Orçamento'">

<script>
window.__itensEstoque = {!! json_encode($itensEstoque, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
window.__clientes     = {!! json_encode($clientes,     JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
window.__submitUrl    = '{{ $orcamento ? route('oficina.orcamentos.update', $orcamento['id']) : route('oficina.orcamentos.store') }}';
window.__csrfToken    = '{{ csrf_token() }}';
window.__orcamento    = {!! $orcamento ? json_encode($orcamento, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : 'null' !!};
</script>

{{-- Tudo dentro do mesmo x-data para que os sheets fixos acessem o estado --}}
<div x-data="novoOrcamento()" x-init="init()">

{{-- ============================================================
     HEADER
============================================================ --}}
<div class="flex items-center gap-2 mb-5">
    <a href="{{ route('oficina.orcamentos.index') }}"
       class="flex items-center gap-1.5 text-sm text-muted hover:text-void transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Orçamentos
    </a>
    <span class="text-muted/40">·</span>
    <span class="font-display font-semibold text-void text-sm" x-text="modoEdicao ? 'Editar ' + codigoOrc : 'Novo Orçamento'">Novo Orçamento</span>
</div>

{{-- ============================================================
     ÁREA DE CONTEÚDO (scrollable)
============================================================ --}}
<div class="max-w-2xl mx-auto pb-36 md:pb-8">

    {{-- ──── STEP 1: CONTEXTO ──── --}}
    <div x-show="step === 1"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-x-2"
         x-transition:enter-end="opacity-100 translate-x-0">

        {{-- Chips de contexto --}}
        <div class="bg-white rounded-2xl mb-3 px-4 pt-4 pb-4"
             style="border:1px solid var(--color-border);box-shadow:0 1px 6px rgba(0,0,0,0.04);">
            <p class="text-xs font-semibold text-muted uppercase tracking-wide mb-3">Vincular orçamento a…</p>

            <div class="grid grid-cols-2 gap-2">
                <button @click="contexto = 'cliente'"
                        class="flex items-center gap-3 p-3 rounded-xl text-left transition-all"
                        :style="contexto === 'cliente' ? 'border:2px solid #3b82f6;background:rgba(59,130,246,0.06);' : 'border:1px solid var(--color-border);background:var(--color-surface);'">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 text-lg" style="background:rgba(59,130,246,0.1);">👤</div>
                    <div>
                        <p class="font-semibold text-sm" :class="contexto === 'cliente' ? 'text-spark' : 'text-void'">Cliente</p>
                        <p class="text-[10px] text-muted leading-tight">Selecionar cliente</p>
                    </div>
                </button>

                <button @click="contexto = 'veiculo'"
                        class="flex items-center gap-3 p-3 rounded-xl text-left transition-all"
                        :style="contexto === 'veiculo' ? 'border:2px solid #3b82f6;background:rgba(59,130,246,0.06);' : 'border:1px solid var(--color-border);background:var(--color-surface);'">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 text-lg" style="background:rgba(100,116,139,0.1);">🚗</div>
                    <div>
                        <p class="font-semibold text-sm" :class="contexto === 'veiculo' ? 'text-spark' : 'text-void'">Veículo</p>
                        <p class="text-[10px] text-muted leading-tight">Buscar por placa</p>
                    </div>
                </button>

                <button @click="contexto = 'os'"
                        class="flex items-center gap-3 p-3 rounded-xl text-left transition-all"
                        :style="contexto === 'os' ? 'border:2px solid #3b82f6;background:rgba(59,130,246,0.06);' : 'border:1px solid var(--color-border);background:var(--color-surface);'">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 text-lg" style="background:rgba(124,58,237,0.1);">📋</div>
                    <div>
                        <p class="font-semibold text-sm" :class="contexto === 'os' ? 'text-spark' : 'text-void'">OS aberta</p>
                        <p class="text-[10px] text-muted leading-tight">Vincular à OS</p>
                    </div>
                </button>

                <button @click="contexto = 'avulso'"
                        class="flex items-center gap-3 p-3 rounded-xl text-left transition-all"
                        :style="contexto === 'avulso' ? 'border:2px solid #3b82f6;background:rgba(59,130,246,0.06);' : 'border:1px solid var(--color-border);background:var(--color-surface);'">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 text-lg" style="background:rgba(16,185,129,0.1);">📄</div>
                    <div>
                        <p class="font-semibold text-sm" :class="contexto === 'avulso' ? 'text-spark' : 'text-void'">Avulso</p>
                        <p class="text-[10px] text-muted leading-tight">Sem vínculo / PDF</p>
                    </div>
                </button>
            </div>

            {{-- Sub-formulário: Cliente --}}
            <div x-show="contexto === 'cliente'" class="mt-3" x-transition>
                <div x-show="!clienteSelecionado">
                    <div class="relative mb-2">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" style="color:#94a3b8;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                        <input type="text" x-model="buscaCliente" placeholder="Buscar cliente…"
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm text-void placeholder-muted focus:outline-none focus:ring-2 transition-colors"
                               style="border:1px solid var(--color-border);background:var(--color-surface);">
                    </div>
                    <div x-show="buscaCliente.trim().length > 0" class="rounded-xl overflow-hidden" style="border:1px solid var(--color-border);">
                        <template x-for="cli in clientesFiltrados" :key="cli.id">
                            <button @click="clienteSelecionado = cli; buscaCliente = ''"
                                    class="w-full flex items-center gap-3 px-3 py-3 hover:bg-surface transition-colors text-left"
                                    style="border-bottom:1px solid rgba(0,0,0,0.05);">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold text-white" style="background:#1E3A5F;">
                                    <span x-text="cli.nome.charAt(0)"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-void text-sm truncate" x-text="cli.nome"></p>
                                    <p class="text-xs text-muted" x-text="cli.telefone"></p>
                                </div>
                            </button>
                        </template>
                        <div x-show="clientesFiltrados.length === 0" class="px-4 py-3 text-sm text-muted text-center">Nenhum cliente encontrado.</div>
                    </div>
                </div>
                <div x-show="clienteSelecionado" class="flex items-center gap-3 px-3 py-3 rounded-xl"
                     style="background:rgba(59,130,246,0.05);border:1.5px solid rgba(59,130,246,0.2);">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold text-white" style="background:#1E3A5F;">
                        <span x-text="clienteSelecionado?.nome?.charAt(0)"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-void text-sm truncate" x-text="clienteSelecionado?.nome"></p>
                        <p class="text-xs text-muted" x-text="clienteSelecionado?.telefone"></p>
                    </div>
                    <button @click="clienteSelecionado = null" class="text-muted hover:text-void flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Sub-formulário: Veículo --}}
            <div x-show="contexto === 'veiculo'" class="mt-3" x-transition>
                <div x-show="!veiculoSelecionado">
                    <input type="text" x-model="buscaPlaca" placeholder="Buscar por placa (ex: ABC-1234)…"
                           class="w-full px-4 py-2.5 rounded-xl text-sm font-mono text-void placeholder-muted focus:outline-none focus:ring-2 transition-colors mb-2"
                           style="border:1px solid var(--color-border);background:var(--color-surface);">
                    <div x-show="buscaPlaca.trim().length > 0" class="rounded-xl overflow-hidden" style="border:1px solid var(--color-border);">
                        <template x-for="v in veiculosFiltrados" :key="v.id">
                            <button @click="veiculoSelecionado = v; buscaPlaca = ''"
                                    class="w-full flex items-center gap-3 px-3 py-3 hover:bg-surface transition-colors text-left"
                                    style="border-bottom:1px solid rgba(0,0,0,0.05);">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-sm" style="background:rgba(100,116,139,0.1);">🚗</div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-mono font-bold text-void text-sm" x-text="v.placa"></p>
                                    <p class="text-xs text-muted" x-text="v.modelo + ' · ' + v.cliente"></p>
                                </div>
                            </button>
                        </template>
                        <div x-show="veiculosFiltrados.length === 0" class="px-4 py-3 text-sm text-muted text-center">Nenhum veículo encontrado.</div>
                    </div>
                </div>
                <div x-show="veiculoSelecionado" class="flex items-center gap-3 px-3 py-3 rounded-xl"
                     style="background:rgba(59,130,246,0.05);border:1.5px solid rgba(59,130,246,0.2);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-sm" style="background:rgba(100,116,139,0.12);">🚗</div>
                    <div class="flex-1 min-w-0">
                        <p class="font-mono font-bold text-void text-sm" x-text="veiculoSelecionado?.placa"></p>
                        <p class="text-xs text-muted" x-text="veiculoSelecionado?.modelo + ' · ' + veiculoSelecionado?.cliente"></p>
                    </div>
                    <button @click="veiculoSelecionado = null" class="text-muted hover:text-void flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Sub-formulário: OS aberta --}}
            <div x-show="contexto === 'os'" class="mt-3 space-y-2" x-transition>
                <template x-for="os in osAbertas" :key="os.id">
                    <button @click="osSelecionada = osSelecionada === os.id ? null : os.id"
                            class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-left transition-all"
                            :style="osSelecionada === os.id ? 'border:2px solid #3b82f6;background:rgba(59,130,246,0.05);' : 'border:1px solid var(--color-border);background:var(--color-surface);'">
                        <div class="flex-1 min-w-0">
                            <span class="font-mono font-semibold text-sm" :class="osSelecionada === os.id ? 'text-spark' : 'text-void'" x-text="os.id"></span>
                            <span class="text-muted text-xs ml-2" x-text="os.cliente + ' · ' + os.veiculo"></span>
                        </div>
                        <svg x-show="osSelecionada === os.id" class="w-4 h-4 flex-shrink-0" style="color:#3b82f6;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </template>
            </div>
        </div>

        {{-- Validade --}}
        <div class="bg-white rounded-2xl mb-4 px-4 pt-4 pb-4"
             style="border:1px solid var(--color-border);box-shadow:0 1px 6px rgba(0,0,0,0.04);">
            <p class="text-xs font-semibold text-muted uppercase tracking-wide mb-3">Validade do orçamento</p>
            <div class="flex items-center gap-2 flex-wrap">
                <template x-for="dias in [7, 15, 30]" :key="dias">
                    <button @click="setValidade(dias)"
                            class="px-3 py-2 rounded-lg text-xs font-semibold transition-all"
                            :class="validadeDias === dias ? 'text-white' : 'text-muted hover:text-void'"
                            :style="validadeDias === dias ? 'background:#0f172a;' : 'border:1px solid var(--color-border);background:var(--color-surface);'"
                            x-text="'+' + dias + ' dias'">
                    </button>
                </template>
                <span class="text-muted text-xs">ou</span>
                <input type="date" x-model="validadeData" @change="validadeDias = null"
                       class="px-3 py-2 rounded-lg text-xs text-void focus:outline-none focus:ring-2 transition-colors"
                       style="border:1px solid var(--color-border);background:var(--color-surface);">
            </div>
            <p class="text-xs text-muted mt-2" x-show="validadeData"
               x-text="'Válido até: ' + new Date(validadeData + 'T12:00:00').toLocaleDateString('pt-BR')"></p>
        </div>

        <button @click="continuar()" :disabled="!contexto"
                class="w-full py-3.5 rounded-xl text-sm font-bold text-white transition-all"
                :style="contexto ? 'background:#0f172a;' : 'background:#94a3b8;opacity:.5;cursor:not-allowed;'">
            Continuar →
        </button>
    </div>{{-- /step 1 --}}

    {{-- ──── STEP 2: MONTAGEM ──── --}}
    <div x-show="step === 2"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-x-2"
         x-transition:enter-end="opacity-100 translate-x-0">

        {{-- Header step 2 --}}
        <div class="flex items-center gap-2 mb-4">
            <button @click="step = 1" class="text-muted hover:text-void transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-void text-sm truncate" x-text="headerLabel"></p>
                <p class="text-muted text-[11px]">Montando orçamento</p>
            </div>
            <span class="font-mono text-xs px-2 py-1 rounded-lg font-bold" style="background:rgba(59,130,246,0.08);color:#3b82f6;"
                  x-text="modoEdicao ? codigoOrc : 'NOVO'">NOVO</span>
        </div>

        {{-- ── Peças ── --}}
        <div class="bg-white rounded-2xl mb-3" style="border:1px solid rgba(59,130,246,0.2);box-shadow:0 1px 6px rgba(0,0,0,0.04);">
            <div class="flex items-center justify-between px-4 py-3" style="background:rgba(59,130,246,0.02);border-radius:1rem 1rem 0 0;">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-void">🔧 Peças</span>
                    <span x-show="pecas.length > 0" class="text-[10px] px-1.5 py-0.5 rounded-full font-semibold"
                          style="background:rgba(59,130,246,0.1);color:#3b82f6;"
                          x-text="pecas.length + (pecas.length === 1 ? ' item' : ' itens')"></span>
                </div>
                <button @click="sheetPecasAberto = true"
                        class="text-xs font-bold px-2.5 py-1 rounded-lg"
                        style="background:rgba(59,130,246,0.1);color:#3b82f6;">
                    + Adicionar
                </button>
            </div>

            <div x-show="pecas.length === 0" class="px-4 py-5 text-center" style="border-top:1px solid rgba(59,130,246,0.08);">
                <p class="text-muted text-xs">Nenhuma peça. Toque em "+ Adicionar".</p>
            </div>

            <div x-show="pecas.length > 0" style="border-top:1px solid rgba(59,130,246,0.08);">
                <template x-for="(peca, i) in pecas" :key="peca.id">
                    <div class="flex items-center gap-3 px-4 py-3"
                         :style="i < pecas.length - 1 ? 'border-bottom:1px solid rgba(0,0,0,0.04);' : ''">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-void text-sm truncate" x-text="peca.nome"></p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <div class="flex items-center gap-1 rounded-md px-1.5 py-0.5" style="border:1px solid var(--color-border);background:var(--color-surface);">
                                    <button @click="peca.qtd = Math.max(1, peca.qtd - 1)" class="w-5 h-5 flex items-center justify-center text-muted hover:text-void">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                    </button>
                                    <span class="font-mono text-xs font-bold w-4 text-center text-void" x-text="peca.qtd"></span>
                                    <button @click="peca.qtd++" class="w-5 h-5 flex items-center justify-center text-muted hover:text-void">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>
                                <span class="text-muted text-xs">×</span>
                                <div class="flex items-center rounded-md px-2 py-0.5" style="border:1px solid var(--color-border);background:var(--color-surface);">
                                    <span class="text-xs text-muted mr-1">R$</span>
                                    <input type="number" x-model="peca.preco" step="0.01" class="w-14 font-mono text-xs text-void bg-transparent focus:outline-none">
                                </div>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-mono font-bold text-void text-sm"
                               x-text="'R$ ' + (peca.qtd * parseFloat(peca.preco||0)).toLocaleString('pt-BR',{minimumFractionDigits:2})"></p>
                            <button @click="pecas.splice(i, 1)" class="mt-1 text-[10px] font-semibold" style="color:#fca5a5;">remover</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ── Mão de obra ── --}}
        <div x-show="!maoDeObra.ativa" class="bg-white rounded-2xl mb-3 flex items-center justify-between px-4 py-3"
             style="border:1px dashed rgba(100,116,139,0.25);">
            <span class="text-sm font-bold text-muted">🛠 Mão de obra</span>
            <button @click="maoDeObra.ativa = true" class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background:rgba(59,130,246,0.1);color:#3b82f6;">+ Adicionar</button>
        </div>
        <div x-show="maoDeObra.ativa" class="bg-white rounded-2xl mb-3" style="border:1px solid rgba(59,130,246,0.2);box-shadow:0 1px 6px rgba(0,0,0,0.04);">
            <div class="flex items-center justify-between px-4 py-3" style="background:rgba(59,130,246,0.02);border-radius:1rem 1rem 0 0;">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-void">🛠 Mão de obra</span>
                    <span x-show="maoDeObra.modo === 'detalhado'" class="text-[10px] px-1.5 py-0.5 rounded-full font-semibold" style="background:rgba(59,130,246,0.1);color:#3b82f6;">detalhada</span>
                </div>
                <div class="flex items-center gap-2">
                    <button x-show="maoDeObra.modo === 'simples'" @click="maoDeObra.modo = 'detalhado'" class="text-xs font-bold px-2 py-0.5 rounded-md" style="background:rgba(59,130,246,0.1);color:#3b82f6;">Detalhar ›</button>
                    <button x-show="maoDeObra.modo === 'detalhado'" @click="maoDeObra.modo = 'simples'; maoDeObra.itens = []" class="text-xs text-muted hover:text-void transition-colors">← simples</button>
                </div>
            </div>
            <div style="border-top:1px solid rgba(59,130,246,0.08);">
                <div x-show="maoDeObra.modo === 'simples'" class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-muted font-medium">R$</span>
                        <input type="number" x-model="maoDeObra.valor" step="0.01" placeholder="0,00"
                               class="flex-1 py-2 px-3 rounded-xl text-sm font-mono text-void focus:outline-none focus:ring-2 transition-colors"
                               style="border:1px solid var(--color-border);background:var(--color-surface);">
                    </div>
                </div>
                <div x-show="maoDeObra.modo === 'detalhado'" class="px-4 py-3">
                    <template x-for="(item, i) in maoDeObra.itens" :key="i">
                        <div class="flex items-center justify-between py-2" :style="i < maoDeObra.itens.length - 1 ? 'border-bottom:1px solid rgba(0,0,0,0.04);' : ''">
                            <span class="text-sm text-void font-medium truncate flex-1" x-text="item.desc"></span>
                            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                <span class="font-mono text-sm font-bold text-void" x-text="'R$ ' + parseFloat(item.valor||0).toLocaleString('pt-BR',{minimumFractionDigits:2})"></span>
                                <button @click="maoDeObra.itens.splice(i, 1)" class="text-[10px] font-semibold" style="color:#fca5a5;">✕</button>
                            </div>
                        </div>
                    </template>
                    <div @click="abrirNovoServico('maoDeObra')" class="mt-2 flex items-center gap-2 cursor-pointer"
                         style="border:1.5px dashed rgba(59,130,246,0.35);border-radius:8px;padding:7px 10px;">
                        <div class="flex items-center justify-center rounded font-bold text-sm flex-shrink-0"
                             style="width:18px;height:18px;background:rgba(59,130,246,0.12);color:#3b82f6;">+</div>
                        <span class="text-xs font-semibold" style="color:#3b82f6;">Novo serviço</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Retífica ── --}}
        <div x-show="!retifica.ativa" class="bg-white rounded-2xl mb-3 flex items-center justify-between px-4 py-3"
             style="border:1px dashed rgba(100,116,139,0.25);">
            <span class="text-sm font-bold text-muted">⚙ Retífica</span>
            <button @click="retifica.ativa = true" class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background:rgba(59,130,246,0.1);color:#3b82f6;">+ Adicionar</button>
        </div>
        <div x-show="retifica.ativa" class="bg-white rounded-2xl mb-3" style="border:1px solid rgba(59,130,246,0.2);box-shadow:0 1px 6px rgba(0,0,0,0.04);">
            <div class="flex items-center justify-between px-4 py-3" style="background:rgba(59,130,246,0.02);border-radius:1rem 1rem 0 0;">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-void">⚙ Retífica</span>
                    <span x-show="retifica.modo === 'detalhado'" class="text-[10px] px-1.5 py-0.5 rounded-full font-semibold" style="background:rgba(59,130,246,0.1);color:#3b82f6;">detalhada</span>
                </div>
                <div class="flex items-center gap-2">
                    <button x-show="retifica.modo === 'simples'" @click="retifica.modo = 'detalhado'" class="text-xs font-bold px-2 py-0.5 rounded-md" style="background:rgba(59,130,246,0.1);color:#3b82f6;">Detalhar ›</button>
                    <button x-show="retifica.modo === 'detalhado'" @click="retifica.modo = 'simples'; retifica.itens = []" class="text-xs text-muted hover:text-void transition-colors">← simples</button>
                    <button @click="retifica.ativa = false; retifica.modo = 'simples'; retifica.valor = ''; retifica.itens = []" class="text-muted hover:text-void transition-colors ml-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div style="border-top:1px solid rgba(59,130,246,0.08);">
                <div x-show="retifica.modo === 'simples'" class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-muted font-medium">R$</span>
                        <input type="number" x-model="retifica.valor" step="0.01" placeholder="0,00"
                               class="flex-1 py-2 px-3 rounded-xl text-sm font-mono text-void focus:outline-none focus:ring-2 transition-colors"
                               style="border:1px solid var(--color-border);background:var(--color-surface);">
                    </div>
                </div>
                <div x-show="retifica.modo === 'detalhado'" class="px-4 py-3">
                    <template x-for="(item, i) in retifica.itens" :key="i">
                        <div class="flex items-center justify-between py-2" :style="i < retifica.itens.length - 1 ? 'border-bottom:1px solid rgba(0,0,0,0.04);' : ''">
                            <span class="text-sm text-void font-medium truncate flex-1" x-text="item.desc"></span>
                            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                <span class="font-mono text-sm font-bold text-void" x-text="'R$ ' + parseFloat(item.valor||0).toLocaleString('pt-BR',{minimumFractionDigits:2})"></span>
                                <button @click="retifica.itens.splice(i, 1)" class="text-[10px] font-semibold" style="color:#fca5a5;">✕</button>
                            </div>
                        </div>
                    </template>
                    <div @click="abrirNovoServico('retifica')" class="mt-2 flex items-center gap-2 cursor-pointer"
                         style="border:1.5px dashed rgba(59,130,246,0.35);border-radius:8px;padding:7px 10px;">
                        <div class="flex items-center justify-center rounded font-bold text-sm flex-shrink-0"
                             style="width:18px;height:18px;background:rgba(59,130,246,0.12);color:#3b82f6;">+</div>
                        <span class="text-xs font-semibold" style="color:#3b82f6;">Novo serviço</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Outros ── --}}
        <div x-show="!outros.ativa" class="bg-white rounded-2xl mb-3 flex items-center justify-between px-4 py-3"
             style="border:1px dashed rgba(100,116,139,0.25);">
            <span class="text-sm font-bold text-muted">➕ Outros serviços</span>
            <button @click="outros.ativa = true" class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background:rgba(59,130,246,0.1);color:#3b82f6;">+ Adicionar</button>
        </div>
        <div x-show="outros.ativa" class="bg-white rounded-2xl mb-3" style="border:1px solid rgba(59,130,246,0.2);box-shadow:0 1px 6px rgba(0,0,0,0.04);">
            <div class="flex items-center justify-between px-4 py-3" style="background:rgba(59,130,246,0.02);border-radius:1rem 1rem 0 0;">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-void">➕ Outros serviços</span>
                    <span x-show="outros.modo === 'detalhado'" class="text-[10px] px-1.5 py-0.5 rounded-full font-semibold" style="background:rgba(59,130,246,0.1);color:#3b82f6;">detalhado</span>
                </div>
                <div class="flex items-center gap-2">
                    <button x-show="outros.modo === 'simples'" @click="outros.modo = 'detalhado'" class="text-xs font-bold px-2 py-0.5 rounded-md" style="background:rgba(59,130,246,0.1);color:#3b82f6;">Detalhar ›</button>
                    <button x-show="outros.modo === 'detalhado'" @click="outros.modo = 'simples'; outros.itens = []" class="text-xs text-muted hover:text-void transition-colors">← simples</button>
                    <button @click="outros.ativa = false; outros.modo = 'simples'; outros.valor = ''; outros.itens = []" class="text-muted hover:text-void transition-colors ml-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div style="border-top:1px solid rgba(59,130,246,0.08);">
                <div x-show="outros.modo === 'simples'" class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-muted font-medium">R$</span>
                        <input type="number" x-model="outros.valor" step="0.01" placeholder="0,00"
                               class="flex-1 py-2 px-3 rounded-xl text-sm font-mono text-void focus:outline-none focus:ring-2 transition-colors"
                               style="border:1px solid var(--color-border);background:var(--color-surface);">
                    </div>
                </div>
                <div x-show="outros.modo === 'detalhado'" class="px-4 py-3">
                    <template x-for="(item, i) in outros.itens" :key="i">
                        <div class="flex items-center justify-between py-2" :style="i < outros.itens.length - 1 ? 'border-bottom:1px solid rgba(0,0,0,0.04);' : ''">
                            <span class="text-sm text-void font-medium truncate flex-1" x-text="item.desc"></span>
                            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                <span class="font-mono text-sm font-bold text-void" x-text="'R$ ' + parseFloat(item.valor||0).toLocaleString('pt-BR',{minimumFractionDigits:2})"></span>
                                <button @click="outros.itens.splice(i, 1)" class="text-[10px] font-semibold" style="color:#fca5a5;">✕</button>
                            </div>
                        </div>
                    </template>
                    <div @click="abrirNovoServico('outros')" class="mt-2 flex items-center gap-2 cursor-pointer"
                         style="border:1.5px dashed rgba(59,130,246,0.35);border-radius:8px;padding:7px 10px;">
                        <div class="flex items-center justify-center rounded font-bold text-sm flex-shrink-0"
                             style="width:18px;height:18px;background:rgba(59,130,246,0.12);color:#3b82f6;">+</div>
                        <span class="text-xs font-semibold" style="color:#3b82f6;">Novo serviço</span>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /step 2 --}}

</div>{{-- /max-w-2xl --}}

{{-- ============================================================
     BARRA DE TOTAL FIXO (step 2)
     Dentro do x-data principal — acessa totalFormatado etc.
============================================================ --}}
<div x-show="step === 2"
     class="fixed left-0 right-0 z-30 flex items-center gap-3 px-4 md:px-6 py-3"
     style="bottom:3.5rem;background:#0f172a;border-top:1px solid rgba(255,255,255,0.08);box-shadow:0 -4px 24px rgba(0,0,0,0.25);">
    <div class="flex-1 min-w-0">
        <p class="text-[10px] font-semibold uppercase tracking-wide" style="color:rgba(255,255,255,0.4);">Total</p>
        <p class="font-mono font-bold text-white text-lg leading-tight" x-text="'R$ ' + totalFormatado"></p>
    </div>
    <button @click="salvar('rascunho')"
            class="px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors"
            style="border:1px solid rgba(255,255,255,0.15);color:rgba(255,255,255,0.7);background:rgba(255,255,255,0.05);">
        Rascunho
    </button>
    <button @click="salvar('pendente')"
            class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-opacity"
            :style="totalSecoes > 0 ? 'background:#3b82f6;' : 'background:#3b82f6;opacity:.4;'"
            x-text="modoEdicao ? 'Salvar' : 'Gerar ORC'">
        Gerar ORC
    </button>
</div>

{{-- ============================================================
     BOTTOM SHEET — ADICIONAR PEÇA
============================================================ --}}
<div x-show="sheetPecasAberto"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="sheetPecasAberto = false; buscaPeca = ''"
     class="fixed inset-0 z-40" style="background:rgba(0,0,0,0.5);">
</div>
<div x-show="sheetPecasAberto"
     x-transition:enter="transition ease-out duration-250" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
     class="fixed bottom-0 left-0 right-0 z-50 rounded-t-2xl" style="background:#fff;max-height:85vh;display:flex;flex-direction:column;">

    <div class="flex justify-center pt-3 pb-1 flex-shrink-0">
        <div class="w-10 h-1 rounded-full" style="background:rgba(0,0,0,0.1);"></div>
    </div>
    <div class="px-4 pb-2 flex-shrink-0">
        <p class="font-bold text-void text-base mb-3">Adicionar peça</p>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none" style="color:#94a3b8;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
            <input type="text" x-model="buscaPeca" placeholder="Buscar no estoque…"
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm text-void placeholder-muted focus:outline-none focus:ring-2 transition-colors"
                   style="border:1px solid var(--color-border);background:var(--color-surface);">
        </div>
    </div>
    <div class="overflow-y-auto flex-1 px-4">
        <template x-for="item in resultadosBuscaPeca" :key="item.id">
            <button @click="adicionarPecaEstoque(item)" class="w-full flex items-center justify-between py-3 text-left"
                    style="border-bottom:1px solid rgba(0,0,0,0.04);">
                <div class="flex-1 min-w-0 mr-3">
                    <p class="font-semibold text-void text-sm truncate" x-text="item.descricao"></p>
                    <p class="text-xs text-muted mt-0.5">
                        <span x-text="item.categoria"></span> · <span x-text="item.quantidade"></span> em estoque
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="font-mono font-bold text-void text-sm"
                          x-text="'R$ ' + parseFloat(item.valor_unitario).toLocaleString('pt-BR',{minimumFractionDigits:2})"></span>
                    <span class="text-xs font-bold px-2 py-1 rounded-lg" style="background:rgba(59,130,246,0.1);color:#3b82f6;">+ Add</span>
                </div>
            </button>
        </template>
        <div x-show="buscaPeca.length > 0 && resultadosBuscaPeca.length === 0" class="py-4 text-center text-muted text-sm">
            Nenhuma peça encontrada no estoque.
        </div>

        <div class="flex items-center gap-3 my-3">
            <div class="flex-1 h-px" style="background:var(--color-border);"></div>
            <span class="text-xs text-muted">ou adicionar manualmente</span>
            <div class="flex-1 h-px" style="background:var(--color-border);"></div>
        </div>

        <div class="mb-3">
            <p class="text-xs text-muted mb-1.5">Nome da peça</p>
            <input type="text" x-model="pecaManualNome" placeholder="Ex: Correia dentada"
                   class="w-full px-3 py-2.5 rounded-xl text-sm text-void focus:outline-none focus:ring-2 transition-colors mb-2"
                   style="border:1px solid var(--color-border);background:var(--color-surface);">
            <div class="flex gap-2">
                <div style="width:72px;">
                    <p class="text-xs text-muted mb-1.5">Qtd</p>
                    <input type="number" x-model="pecaManualQtd" min="1"
                           class="w-full px-3 py-2.5 rounded-xl text-sm font-mono text-void focus:outline-none focus:ring-2 transition-colors"
                           style="border:1px solid var(--color-border);background:var(--color-surface);">
                </div>
                <div class="flex-1">
                    <p class="text-xs text-muted mb-1.5">Valor unitário</p>
                    <div class="flex items-center rounded-xl px-3" style="border:1px solid var(--color-border);background:var(--color-surface);">
                        <span class="text-sm text-muted mr-1">R$</span>
                        <input type="number" x-model="pecaManualValor" step="0.01" placeholder="0,00"
                               class="flex-1 py-2.5 font-mono text-sm text-void bg-transparent focus:outline-none">
                    </div>
                </div>
            </div>
        </div>
        <button @click="adicionarPecaManual()" :disabled="!pecaManualNome.trim()"
                class="w-full py-3 rounded-xl text-sm font-bold text-white mb-4 transition-opacity"
                :style="pecaManualNome.trim() ? 'background:#0f172a;' : 'background:#94a3b8;opacity:.5;'">
            Adicionar peça
        </button>
    </div>
</div>

{{-- ============================================================
     BOTTOM SHEET — NOVO SERVIÇO
============================================================ --}}
<div x-show="sheetServicoAberto"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="sheetServicoAberto = false"
     class="fixed inset-0 z-40" style="background:rgba(0,0,0,0.5);">
</div>
<div x-show="sheetServicoAberto"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
     @click.stop
     class="fixed bottom-0 left-0 right-0 z-50 rounded-t-2xl px-4 pb-8 pt-3" style="background:#fff;">

    <div class="flex justify-center mb-4">
        <div class="w-10 h-1 rounded-full" style="background:rgba(0,0,0,0.1);"></div>
    </div>
    <p class="font-bold text-void text-base mb-4">Adicionar item</p>

    <p class="text-xs text-muted mb-1.5">Descrição</p>
    <input type="text" x-model="novoServicoDesc" placeholder="Ex: Alinhamento dianteiro"
           class="w-full px-3 py-2.5 rounded-xl text-sm text-void focus:outline-none focus:ring-2 transition-colors mb-3"
           style="border:1px solid var(--color-border);background:var(--color-surface);">

    <p class="text-xs text-muted mb-1.5">Valor</p>
    <div class="flex items-center rounded-xl px-3 mb-4" style="border:1px solid var(--color-border);background:var(--color-surface);">
        <span class="text-sm text-muted mr-1">R$</span>
        <input type="number" x-model="novoServicoValor" step="0.01" placeholder="0,00"
               class="flex-1 py-2.5 font-mono text-sm text-void bg-transparent focus:outline-none">
    </div>

    <button @click="confirmarNovoServico()" :disabled="!novoServicoDesc.trim()"
            class="w-full py-3 rounded-xl text-sm font-bold text-white transition-opacity"
            :style="novoServicoDesc.trim() ? 'background:#3b82f6;' : 'background:#94a3b8;opacity:.5;'">
        Adicionar
    </button>
</div>

</div>{{-- /x-data --}}

<script>
function novoOrcamento() {
    return {
        step: 1,

        modoEdicao: false,
        codigoOrc:  '',
        orcId:      null,

        contexto: null,
        buscaCliente: '',
        clienteSelecionado: null,
        buscaPlaca: '',
        veiculoSelecionado: null,
        osSelecionada: null,
        osAbertas: [],

        validadeDias: 7,
        validadeData: '',

        pecas: [],
        maoDeObra: { ativa: false, modo: 'simples', valor: '', itens: [] },
        retifica:   { ativa: false, modo: 'simples', valor: '', itens: [] },
        outros:     { ativa: false, modo: 'simples', valor: '', itens: [] },

        sheetPecasAberto: false,
        buscaPeca: '',
        pecaManualNome: '',
        pecaManualQtd: '1',
        pecaManualValor: '',

        sheetServicoAberto: false,
        sheetServicoSecao: null,
        novoServicoDesc: '',
        novoServicoValor: '',

        itensEstoque: [],
        clientes: [],
        veiculosMock: [],

        get resultadosBuscaPeca() {
            const lista = this.itensEstoque.filter(i => i.quantidade > 0);
            if (!this.buscaPeca.trim()) return lista.slice(0, 6);
            const q = this.buscaPeca.toLowerCase();
            return lista.filter(i => i.descricao.toLowerCase().includes(q)).slice(0, 8);
        },

        get clientesFiltrados() {
            if (!this.buscaCliente.trim()) return [];
            const q = this.buscaCliente.toLowerCase();
            return this.clientes.filter(c => c.nome.toLowerCase().includes(q) || (c.telefone || '').includes(q));
        },

        get veiculosFiltrados() {
            if (!this.buscaPlaca.trim()) return [];
            const q = this.buscaPlaca.toLowerCase().replace(/[^a-z0-9]/g, '');
            return this.veiculosMock.filter(v =>
                v.placa.toLowerCase().replace(/[^a-z0-9]/g, '').includes(q) ||
                v.modelo.toLowerCase().includes(q)
            );
        },

        get headerLabel() {
            if (this.clienteSelecionado) return this.clienteSelecionado.nome;
            if (this.veiculoSelecionado) return this.veiculoSelecionado.placa + ' · ' + this.veiculoSelecionado.modelo;
            if (this.osSelecionada) return this.osSelecionada;
            return 'Orçamento avulso';
        },

        get totalSecoes() {
            let t = 0;
            this.pecas.forEach(p => { t += parseFloat(p.preco || 0) * parseInt(p.qtd || 1); });
            for (const s of [this.maoDeObra, this.retifica, this.outros]) {
                if (!s.ativa) continue;
                if (s.modo === 'simples') t += parseFloat(s.valor || 0);
                else s.itens.forEach(i => { t += parseFloat(i.valor || 0); });
            }
            return t;
        },

        get totalFormatado() {
            return this.totalSecoes.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        },

        init() {
            this.itensEstoque = window.__itensEstoque || [];
            this.clientes     = window.__clientes     || [];
            this.osAbertas    = [
                { id: 'OS-2024-018', cliente: 'João Silva',     veiculo: 'Honda Civic' },
                { id: 'OS-2024-019', cliente: 'Maria Oliveira', veiculo: 'Toyota Corolla' },
                { id: 'OS-2024-020', cliente: 'Carlos Santos',  veiculo: 'VW Polo' },
            ];
            this.veiculosMock = [
                { id: 1, placa: 'ABC-1234', modelo: 'Honda Civic 2019',    cliente: 'João Silva' },
                { id: 2, placa: 'DEF-5678', modelo: 'Toyota Corolla 2021', cliente: 'Maria Oliveira' },
                { id: 3, placa: 'GHI-9012', modelo: 'VW Polo 2022',        cliente: 'Carlos Santos' },
            ];

            const orc = window.__orcamento || null;
            if (orc) {
                // Modo edição: semeia o wizard com o orçamento existente e já abre a montagem.
                this.modoEdicao = true;
                this.codigoOrc  = orc.codigo;
                this.orcId      = orc.id;
                this.contexto   = orc.contexto;
                this.clienteSelecionado = orc.cliente || null;
                this.veiculoSelecionado = orc.veiculo || null;
                this.osSelecionada      = orc.os_vinculada || null;
                this.validadeData       = orc.validade || '';
                this.validadeDias       = null;
                this.pecas = (orc.pecas || []).map(p => ({ ...p }));
                if ((orc.servicos || []).length) {
                    this.maoDeObra.ativa = true;
                    this.maoDeObra.modo  = 'detalhado';
                    this.maoDeObra.itens = orc.servicos.map(s => ({ ...s }));
                }
                this.step = 2;
            } else {
                this.setValidade(7);
            }
        },

        setValidade(dias) {
            this.validadeDias = dias;
            const d = new Date();
            d.setDate(d.getDate() + dias);
            this.validadeData = d.toISOString().split('T')[0];
        },

        continuar() {
            if (!this.contexto) return;
            this.step = 2;
        },

        adicionarPecaEstoque(item) {
            const existente = this.pecas.find(p => p.id === item.id && !p.manual);
            if (existente) {
                existente.qtd++;
            } else {
                this.pecas.push({ id: item.id, nome: item.descricao, categoria: item.categoria, preco: item.valor_unitario, qtd: 1, manual: false });
            }
            this.sheetPecasAberto = false;
            this.buscaPeca = '';
        },

        adicionarPecaManual() {
            if (!this.pecaManualNome.trim()) return;
            this.pecas.push({ id: Date.now(), nome: this.pecaManualNome.trim(), categoria: null, preco: parseFloat(this.pecaManualValor) || 0, qtd: parseInt(this.pecaManualQtd) || 1, manual: true });
            this.pecaManualNome = '';
            this.pecaManualQtd  = '1';
            this.pecaManualValor = '';
            this.sheetPecasAberto = false;
        },

        abrirNovoServico(secao) {
            this.sheetServicoSecao  = secao;
            this.novoServicoDesc    = '';
            this.novoServicoValor   = '';
            this.sheetServicoAberto = true;
        },

        confirmarNovoServico() {
            if (!this.novoServicoDesc.trim()) return;
            this[this.sheetServicoSecao].itens.push({ desc: this.novoServicoDesc.trim(), valor: parseFloat(this.novoServicoValor) || 0 });
            this.sheetServicoAberto = false;
        },

        salvar(status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.__submitUrl;
            const campos = {
                _token:       window.__csrfToken,
                acao:         this.modoEdicao ? 'editar' : 'criar',
                status,
                contexto:     this.contexto,
                cliente_id:   this.clienteSelecionado?.id ?? '',
                veiculo_id:   this.veiculoSelecionado?.id ?? '',
                os_vinculada: this.osSelecionada ?? '',
                validade:     this.validadeData,
                pecas:        JSON.stringify(this.pecas),
                mao_de_obra:  JSON.stringify(this.maoDeObra),
                retifica:     JSON.stringify(this.retifica),
                outros:       JSON.stringify(this.outros),
                total:        this.totalSecoes,
            };
            for (const [k, v] of Object.entries(campos)) {
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = k; inp.value = v;
                form.appendChild(inp);
            }
            document.body.appendChild(form);
            form.submit();
        },
    };
}
</script>

</x-layouts.oficina>
