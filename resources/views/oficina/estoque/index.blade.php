<x-layouts.oficina title="Estoque">

<script>
    window.__itens      = {!! json_encode($itens,      JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    window.__metricas   = {!! json_encode($metricas,   JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    window.__categorias = {!! json_encode($categorias, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
</script>

<div x-data="estoquePage()" x-init="init()">

    {{-- ==================== HEADER ==================== --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2 flex-wrap">
            <h2 class="font-display font-bold text-void text-xl">Estoque</h2>
            <span class="font-mono text-xs px-2 py-0.5 rounded-full font-semibold"
                  style="background:rgba(59,130,246,0.1);color:#3B82F6;"
                  x-text="metricas.total + ' itens'"></span>
            <span x-show="(metricas.baixo + metricas.sem_estoque) > 0"
                  class="font-mono text-xs px-2 py-0.5 rounded-full font-semibold"
                  style="background:rgba(245,158,11,0.12);color:#D97706;"
                  x-text="(metricas.baixo + metricas.sem_estoque) + ' alerta(s)'"></span>
        </div>
        {{-- desktop: botão novo item --}}
        <button @click="abrirSheet('nova-peca')"
                class="hidden md:inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-white text-sm font-medium transition-colors"
                style="background:var(--color-spark);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Item
        </button>
    </div>

    {{-- ==================== MÉTRICAS ==================== --}}
    {{-- Mobile: 2 cards compactos --}}
    <div class="md:hidden grid grid-cols-2 gap-3 mb-4">
        <div class="bg-white rounded-xl px-4 py-3" style="border:1px solid rgba(0,0,0,0.06);box-shadow:0 1px 4px rgba(0,0,0,0.04);">
            <p class="text-muted text-[10px] font-semibold uppercase tracking-wide mb-1">Valor em estoque</p>
            <p class="font-mono font-bold text-void text-base"
               x-text="'R$ ' + metricas.valor_total.toLocaleString('pt-BR',{minimumFractionDigits:0})"></p>
        </div>
        <div class="bg-white rounded-xl px-4 py-3" style="border:1px solid rgba(0,0,0,0.06);box-shadow:0 1px 4px rgba(0,0,0,0.04);"
             :style="metricas.sem_estoque > 0 ? 'border-color:rgba(239,68,68,0.25);' : ''">
            <p class="text-muted text-[10px] font-semibold uppercase tracking-wide mb-1">Sem estoque</p>
            <p class="font-mono font-bold text-base"
               :class="metricas.sem_estoque > 0 ? 'text-red-600' : 'text-void'"
               x-text="metricas.sem_estoque"></p>
        </div>
    </div>

    {{-- Desktop: 4 cards --}}
    <div class="hidden md:grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5" style="border:1px solid var(--color-border);">
            <p class="text-muted text-xs font-medium uppercase tracking-wide mb-1">Total de Itens</p>
            <p class="font-display font-bold text-void text-2xl" x-text="metricas.total"></p>
        </div>
        <div class="bg-white rounded-xl p-5" style="border:1px solid var(--color-border);">
            <p class="text-muted text-xs font-medium uppercase tracking-wide mb-1">Estoque Baixo</p>
            <p class="font-display font-bold text-2xl" :class="metricas.baixo > 0 ? 'text-amber-600' : 'text-void'" x-text="metricas.baixo"></p>
        </div>
        <div class="bg-white rounded-xl p-5" style="border:1px solid var(--color-border);">
            <p class="text-muted text-xs font-medium uppercase tracking-wide mb-1">Sem Estoque</p>
            <p class="font-display font-bold text-2xl" :class="metricas.sem_estoque > 0 ? 'text-red-600' : 'text-void'" x-text="metricas.sem_estoque"></p>
        </div>
        <div class="bg-white rounded-xl p-5" style="border:1px solid var(--color-border);">
            <p class="text-muted text-xs font-medium uppercase tracking-wide mb-1">Valor em Estoque</p>
            <p class="font-display font-bold text-void text-2xl"
               x-text="'R$ ' + metricas.valor_total.toLocaleString('pt-BR',{minimumFractionDigits:2})"></p>
        </div>
    </div>

    {{-- ==================== TABS ==================== --}}
    <div class="flex gap-0 rounded-xl p-1 mb-4" style="background:rgba(0,0,0,0.06);">
        <template x-for="t in [{id:'pecas',label:'Peças'},{id:'orcamentos',label:'Orçamentos'},{id:'balanco',label:'Balanço'}]" :key="t.id">
            <button @click="tab = t.id"
                    class="flex-1 py-2 rounded-lg text-xs font-semibold transition-all duration-200"
                    :class="tab === t.id
                        ? 'bg-white text-void shadow-sm'
                        : 'text-muted hover:text-void'"
                    x-text="t.label">
            </button>
        </template>
    </div>

    {{-- ==================== ABA PEÇAS ==================== --}}
    <div x-show="tab === 'pecas'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

        {{-- Busca --}}
        <div class="relative mb-3">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
            <input type="text" x-model="busca" placeholder="Buscar peça…"
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors"
                   style="border:1px solid var(--color-border);background:#fff;">
        </div>

        {{-- Pills de categoria --}}
        <div class="flex gap-2 mb-3 overflow-x-auto pb-1 scrollbar-hide">
            <button @click="categoriaAtiva = 'todos'"
                    class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold transition-all"
                    :class="categoriaAtiva === 'todos' ? 'bg-void text-white' : 'bg-white text-muted hover:text-void'"
                    :style="categoriaAtiva === 'todos' ? '' : 'border:1px solid var(--color-border);'">
                Todos
            </button>
            <template x-for="cat in categorias" :key="cat">
                <button @click="categoriaAtiva = cat"
                        class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold transition-all"
                        :class="categoriaAtiva === cat ? 'bg-void text-white' : 'bg-white text-muted hover:text-void'"
                        :style="categoriaAtiva === cat ? '' : 'border:1px solid var(--color-border);'"
                        x-text="cat">
                </button>
            </template>
        </div>

        {{-- Pills de status --}}
        <div class="flex gap-2 mb-4">
            <template x-for="f in [{id:'todos',label:'Todos'},{id:'baixo',label:'Baixo'},{id:'sem_estoque',label:'Zerado'}]" :key="f.id">
                <button @click="filtroStatus = f.id"
                        class="px-2.5 py-1 rounded-lg text-xs font-medium transition-all"
                        :class="filtroStatus === f.id ? 'bg-void text-white' : 'bg-white text-muted'"
                        :style="filtroStatus === f.id ? '' : 'border:1px solid var(--color-border);'"
                        x-text="f.label">
                </button>
            </template>
        </div>

        {{-- Estado vazio --}}
        <template x-if="itensFiltrados.length === 0">
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3" style="background:var(--color-surface);border:1px solid var(--color-border);">
                    <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <p class="font-semibold text-void text-sm">Nenhum item encontrado.</p>
            </div>
        </template>

        {{-- MOBILE: cards --}}
        <div class="md:hidden" x-show="itensFiltrados.length > 0">
            <div class="rounded-2xl overflow-hidden" style="border:1px solid rgba(0,0,0,0.06);box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <template x-for="(item, i) in itensFiltrados" :key="item.id">
                    <button @click="abrirDetalhe(item)"
                            class="w-full flex items-center gap-3 px-4 py-4 transition-all duration-150 active:bg-[#F8FAFC] text-left"
                            :style="`background:${corFundoCard(item)};${i < itensFiltrados.length - 1 ? 'border-bottom:1px solid rgba(0,0,0,0.05);' : ''}`">

                        {{-- quantidade em destaque --}}
                        <div class="flex-shrink-0 w-11 text-right">
                            <span class="font-mono font-bold text-2xl leading-none"
                                  :style="`color:${corQtd(item)}`"
                                  x-text="item.quantidade"></span>
                            <p class="text-[9px] text-muted mt-0.5">un</p>
                        </div>

                        {{-- divisor --}}
                        <div class="w-px self-stretch" style="background:rgba(0,0,0,0.07);"></div>

                        {{-- info --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-void text-sm leading-snug truncate" x-text="item.descricao"></p>
                            <p class="text-muted text-xs mt-0.5 truncate" x-text="item.categoria + ' · R$ ' + item.valor_unitario.toLocaleString('pt-BR',{minimumFractionDigits:2})"></p>
                            <div class="mt-1.5 flex items-center gap-1.5">
                                <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full font-semibold"
                                      :style="`background:${corBadgeBg(item)};color:${corQtd(item)}`"
                                      x-text="labelStatus(item)"></span>
                                <span class="text-[10px] text-muted" x-text="item.localizacao"></span>
                            </div>
                        </div>

                        {{-- chevron --}}
                        <svg class="flex-shrink-0 w-4 h-4" style="color:#CBD5E1;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                        </svg>
                    </button>
                </template>
            </div>
        </div>

        {{-- DESKTOP: tabela --}}
        <div class="hidden md:block bg-white rounded-xl overflow-hidden" style="border:1px solid var(--color-border);" x-show="itensFiltrados.length > 0">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--color-border);">
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Descrição</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Categoria</th>
                        <th class="text-center px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Qtd</th>
                        <th class="text-center px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Mín.</th>
                        <th class="text-right px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Custo</th>
                        <th class="text-right px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Venda</th>
                        <th class="text-right px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Margem</th>
                        <th class="text-center px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in itensFiltrados" :key="item.id">
                        <tr class="hover:bg-surface transition-colors cursor-pointer" style="border-bottom:1px solid var(--color-border);"
                            @click="abrirDetalhe(item)">
                            <td class="px-5 py-3.5"><span class="font-medium text-void text-sm" x-text="item.descricao"></span></td>
                            <td class="px-5 py-3.5"><span class="text-xs text-muted" x-text="item.categoria"></span></td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="font-mono font-bold text-sm" :style="`color:${corQtd(item)}`" x-text="item.quantidade"></span>
                            </td>
                            <td class="px-5 py-3.5 text-center"><span class="font-mono text-xs text-muted" x-text="item.estoque_minimo"></span></td>
                            <td class="px-5 py-3.5 text-right"><span class="font-mono text-sm text-muted" x-text="'R$ ' + item.custo.toLocaleString('pt-BR',{minimumFractionDigits:2})"></span></td>
                            <td class="px-5 py-3.5 text-right"><span class="font-mono text-sm text-void" x-text="'R$ ' + item.valor_unitario.toLocaleString('pt-BR',{minimumFractionDigits:2})"></span></td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="font-mono text-sm font-semibold text-emerald-600" x-text="'+' + margem(item) + '%'"></span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium"
                                      :style="`background:${corBadgeBg(item)};color:${corQtd(item)}`"
                                      x-text="labelStatus(item)"></span>
                            </td>
                            <td class="px-5 py-3.5">
                                <button @click.stop="abrirDetalhe(item)" class="text-spark text-xs font-medium hover:underline">Detalhes</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

    </div>

    {{-- ==================== ABA BALANÇO ==================== --}}
    <div x-show="tab === 'balanco'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

        {{-- Sub-tabs --}}
        <div class="flex gap-0 mb-4" style="border-bottom:1px solid var(--color-border);">
            <button @click="subtabBalanco = 'contagem'"
                    class="px-4 py-3 text-sm font-medium transition-colors -mb-px"
                    :class="subtabBalanco === 'contagem' ? 'text-spark border-b-2 border-spark' : 'text-muted hover:text-void'">
                Contagem Física
            </button>
            <button @click="subtabBalanco = 'historico'"
                    class="px-4 py-3 text-sm font-medium transition-colors -mb-px"
                    :class="subtabBalanco === 'historico' ? 'text-spark border-b-2 border-spark' : 'text-muted hover:text-void'">
                Histórico
            </button>
        </div>

        {{-- Contagem física --}}
        <div x-show="subtabBalanco === 'contagem'">
            <div class="rounded-xl px-4 py-3 mb-4 text-xs font-medium" style="background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.18);color:#1e40af;">
                Informe a quantidade real de cada item na prateleira. Divergências são destacadas.
            </div>

            <div class="rounded-2xl overflow-hidden mb-20" style="border:1px solid rgba(0,0,0,0.06);box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <template x-for="(item, i) in itens" :key="item.id">
                    <div class="flex items-center gap-3 px-4 py-3.5 bg-white"
                         :style="`${contemDivergencia(item) ? 'background:rgba(245,158,11,0.04);' : ''}${i < itens.length - 1 ? 'border-bottom:1px solid rgba(0,0,0,0.05);' : ''}`">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-void text-sm truncate" x-text="item.descricao"></p>
                            <p class="text-xs mt-0.5"
                               :class="contemDivergencia(item) ? 'text-amber-600 font-semibold' : 'text-muted'"
                               x-text="contemDivergencia(item) ? 'Sistema: ' + item.quantidade + ' · Real: ' + contagem[item.id] + ' ⚠ divergência' : 'Sistema: ' + item.quantidade + ' un'">
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button @click="contagem[item.id] = Math.max(0, (contagem[item.id] || 0) - 1)"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-muted transition-colors"
                                    style="border:1px solid var(--color-border);background:#f8fafc;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                            </button>
                            <span class="font-mono font-bold text-void text-lg w-7 text-center" x-text="contagem[item.id] ?? item.quantidade"></span>
                            <button @click="contagem[item.id] = (contagem[item.id] ?? item.quantidade) + 1"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-white transition-colors"
                                    style="background:var(--color-spark);border:none;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Botão fixo confirmar --}}
            <div class="fixed bottom-16 md:bottom-0 left-0 right-0 px-4 pb-4 md:relative md:px-0 md:pb-0 md:mb-4" style="z-index:30;">
                <button @click="confirmarBalanco()"
                        class="w-full py-3.5 rounded-xl text-white text-sm font-bold shadow-lg transition-colors"
                        style="background:#0F172A;">
                    Confirmar Balanço
                </button>
            </div>
        </div>

        {{-- Histórico --}}
        <div x-show="subtabBalanco === 'historico'">
            <div class="rounded-2xl overflow-hidden" style="border:1px solid rgba(0,0,0,0.06);box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <template x-for="(h, i) in historico" :key="i">
                    <div class="flex items-start gap-3 px-4 py-3.5 bg-white"
                         :style="i < historico.length - 1 ? 'border-bottom:1px solid rgba(0,0,0,0.05);' : ''">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                             :style="`background:${h.tipo === 'entrada' ? 'rgba(16,185,129,0.1)' : h.tipo === 'ajuste' ? 'rgba(245,158,11,0.1)' : 'rgba(59,130,246,0.1)'}`">
                            <span class="text-sm font-bold"
                                  :style="`color:${h.tipo === 'entrada' ? '#10b981' : h.tipo === 'ajuste' ? '#d97706' : '#3b82f6'}`"
                                  x-text="h.tipo === 'entrada' ? '↓' : h.tipo === 'ajuste' ? '⚖' : '↑'"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-void text-sm truncate" x-text="h.descricao"></p>
                            <p class="text-muted text-xs mt-0.5" x-text="h.origem"></p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-mono text-sm font-bold"
                               :style="`color:${h.tipo === 'entrada' ? '#10b981' : '#3b82f6'}`"
                               x-text="(h.tipo === 'entrada' ? '+' : h.tipo === 'ajuste' ? '±' : '-') + h.qtd"></p>
                            <p class="text-[10px] text-muted mt-0.5" x-text="h.data"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>

    {{-- ==================== ABA ORÇAMENTOS ==================== --}}
    <div x-show="tab === 'orcamentos'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

        <template x-if="orcamentos.length === 0">
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <p class="font-semibold text-void text-sm mb-1">Nenhum orçamento criado.</p>
                <p class="text-muted text-xs">Use o botão + para criar o primeiro.</p>
            </div>
        </template>

        <div class="rounded-2xl overflow-hidden mb-20" style="border:1px solid rgba(0,0,0,0.06);box-shadow:0 2px 8px rgba(0,0,0,0.04);">
            <template x-for="(orc, i) in orcamentos" :key="orc.id">
                <div class="flex items-center gap-3 px-4 py-4 bg-white"
                     :style="i < orcamentos.length - 1 ? 'border-bottom:1px solid rgba(0,0,0,0.05);' : ''">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="font-mono font-bold text-void text-sm" x-text="orc.id"></span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold"
                                  :style="orc.status === 'vinculado' ? 'background:rgba(59,130,246,0.1);color:#3b82f6;' : 'background:rgba(100,116,139,0.1);color:#475569;'"
                                  x-text="orc.status === 'vinculado' ? 'Vinculado · ' + orc.os : 'Avulso · PDF'"></span>
                        </div>
                        <p class="text-muted text-xs" x-text="orc.data"></p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-mono font-bold text-void text-base"
                           x-text="'R$ ' + orc.total.toLocaleString('pt-BR',{minimumFractionDigits:2})"></p>
                        <button class="text-[11px] mt-1 font-semibold" style="color:var(--color-spark);"
                                @click="alert('Fase mock — exportar PDF de ' + orc.id)">
                            Ver PDF →
                        </button>
                    </div>
                </div>
            </template>
        </div>

    </div>

    {{-- ==================== FAB (mobile) ==================== --}}
    <button @click="fabAcao()"
            class="md:hidden fixed right-4 z-30 w-14 h-14 rounded-full text-white flex items-center justify-center shadow-lg transition-transform active:scale-95"
            style="bottom:5rem;background:var(--color-spark);box-shadow:0 4px 16px rgba(59,130,246,0.45);">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
    </button>

    {{-- ==================== OVERLAY ==================== --}}
    <div x-show="sheet !== null"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="fecharSheet()"
         class="fixed inset-0 z-40"
         style="background:rgba(15,23,42,0.5);">
    </div>

    {{-- ==================== SHEET: DETALHE DA PEÇA ==================== --}}
    <div x-show="sheet === 'detalhe'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-2xl"
         style="border-top:1px solid rgba(0,0,0,0.08);">

        <div class="flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 rounded-full" style="background:var(--color-border);"></div>
        </div>

        <div class="px-5 pb-8 pt-2" x-show="pecaSelecionada">
            {{-- Header --}}
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1 min-w-0 pr-3">
                    <h3 class="font-display font-bold text-void text-lg leading-tight" x-text="pecaSelecionada?.descricao"></h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-muted text-xs" x-text="pecaSelecionada?.categoria"></span>
                        <span class="text-muted text-xs">·</span>
                        <span class="text-xs flex items-center gap-1">
                            📍 <span class="text-muted" x-text="pecaSelecionada?.localizacao"></span>
                        </span>
                    </div>
                </div>
                <div class="w-3 h-3 rounded-full flex-shrink-0 mt-1.5"
                     :style="`background:${corQtd(pecaSelecionada)}`"></div>
            </div>

            {{-- Specs grid --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="rounded-xl px-4 py-3" style="background:var(--color-surface);border:1px solid var(--color-border);">
                    <p class="text-[10px] text-muted uppercase tracking-wide mb-1">Custo</p>
                    <p class="font-mono font-bold text-void text-lg" x-text="'R$ ' + (pecaSelecionada?.custo ?? 0).toLocaleString('pt-BR',{minimumFractionDigits:2})"></p>
                </div>
                <div class="rounded-xl px-4 py-3" style="background:var(--color-surface);border:1px solid var(--color-border);">
                    <p class="text-[10px] text-muted uppercase tracking-wide mb-1">Venda</p>
                    <p class="font-mono font-bold text-void text-lg" x-text="'R$ ' + (pecaSelecionada?.valor_unitario ?? 0).toLocaleString('pt-BR',{minimumFractionDigits:2})"></p>
                </div>
                <div class="rounded-xl px-4 py-3" style="background:var(--color-surface);border:1px solid var(--color-border);">
                    <p class="text-[10px] text-muted uppercase tracking-wide mb-1">Margem</p>
                    <p class="font-mono font-bold text-emerald-600 text-lg" x-text="'+' + margem(pecaSelecionada) + '%'"></p>
                </div>
                <div class="rounded-xl px-4 py-3" style="background:var(--color-surface);border:1px solid var(--color-border);">
                    <p class="text-[10px] text-muted uppercase tracking-wide mb-1">Em estoque</p>
                    <p class="font-mono font-bold text-lg" :style="`color:${corQtd(pecaSelecionada)}`" x-text="(pecaSelecionada?.quantidade ?? 0) + ' un'"></p>
                </div>
            </div>

            {{-- Ações --}}
            <div class="grid grid-cols-2 gap-3">
                <button @click="iniciarEntrada()"
                        class="py-3 rounded-xl text-sm font-bold transition-colors"
                        style="border:1.5px solid var(--color-border);background:#f8fafc;color:#0F172A;">
                    ↓ Dar Entrada
                </button>
                <button @click="iniciarSaida()"
                        class="py-3 rounded-xl text-sm font-bold text-white transition-colors"
                        style="background:#0F172A;">
                    ↑ Registrar Saída
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== SHEET: ENTRADA ==================== --}}
    <div x-show="sheet === 'entrada'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-2xl"
         style="border-top:1px solid rgba(0,0,0,0.08);">

        <div class="flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 rounded-full" style="background:var(--color-border);"></div>
        </div>

        <div class="px-5 pb-8 pt-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display font-bold text-void text-lg">Dar Entrada</h3>
                <button @click="sheet = 'detalhe'" class="text-muted hover:text-void text-xs font-medium">← Voltar</button>
            </div>

            <p class="text-sm text-muted mb-5" x-text="pecaSelecionada?.descricao"></p>

            <div class="mb-4">
                <label class="block text-xs text-muted uppercase tracking-wide mb-2">Quantidade recebida</label>
                <div class="flex items-center gap-4 rounded-xl px-4 py-3" style="background:var(--color-surface);border:1px solid var(--color-border);">
                    <button @click="entradaQtd = Math.max(1, entradaQtd - 1)"
                            class="w-9 h-9 rounded-lg flex items-center justify-center" style="border:1px solid var(--color-border);background:#fff;">
                        <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    </button>
                    <span class="font-mono font-bold text-void text-2xl flex-1 text-center" x-text="entradaQtd"></span>
                    <button @click="entradaQtd++"
                            class="w-9 h-9 rounded-lg flex items-center justify-center text-white" style="background:var(--color-spark);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </button>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-xs text-muted uppercase tracking-wide mb-2">Fornecedor (opcional)</label>
                <input type="text" x-model="entradaFornecedor" placeholder="Nome do fornecedor ou nota fiscal…"
                       class="w-full px-3 py-2.5 rounded-xl text-sm focus:outline-none focus:ring-2"
                       style="border:1px solid var(--color-border);background:var(--color-surface);">
            </div>

            <button @click="confirmarEntrada()"
                    class="w-full py-3.5 rounded-xl text-white text-sm font-bold"
                    style="background:#10b981;">
                Confirmar entrada de <span x-text="entradaQtd"></span> un
            </button>
        </div>
    </div>

    {{-- ==================== SHEET: SAÍDA (multi-step) ==================== --}}
    <div x-show="sheet === 'saida'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-2xl"
         style="border-top:1px solid rgba(0,0,0,0.08);">

        <div class="flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 rounded-full" style="background:var(--color-border);"></div>
        </div>

        <div class="px-5 pb-8 pt-2">

            {{-- Step 1: quantidade --}}
            <div x-show="saidaStep === 1">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-display font-bold text-void text-lg">Registrar Saída</h3>
                    <button @click="sheet = 'detalhe'" class="text-muted hover:text-void text-xs font-medium">← Voltar</button>
                </div>
                <p class="text-sm text-muted mb-5" x-text="pecaSelecionada?.descricao"></p>

                <div class="mb-5">
                    <label class="block text-xs text-muted uppercase tracking-wide mb-2">Quantidade</label>
                    <div class="flex items-center gap-4 rounded-xl px-4 py-3" style="background:var(--color-surface);border:1px solid var(--color-border);">
                        <button @click="saidaQtd = Math.max(1, saidaQtd - 1)"
                                class="w-9 h-9 rounded-lg flex items-center justify-center" style="border:1px solid var(--color-border);background:#fff;">
                            <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                        </button>
                        <span class="font-mono font-bold text-void text-2xl flex-1 text-center" x-text="saidaQtd"></span>
                        <button @click="saidaQtd = Math.min(pecaSelecionada?.quantidade ?? 1, saidaQtd + 1)"
                                class="w-9 h-9 rounded-lg flex items-center justify-center text-white" style="background:var(--color-spark);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                    <p class="text-xs text-muted mt-1.5" x-text="'Disponível: ' + (pecaSelecionada?.quantidade ?? 0) + ' un'"></p>
                </div>

                <div class="mb-5">
                    <label class="block text-xs text-muted uppercase tracking-wide mb-3">Para onde vai?</label>
                    <div class="space-y-2.5">
                        <button @click="saidaDestino = 'os'"
                                class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold text-left transition-all"
                                :style="saidaDestino === 'os' ? 'border:2px solid #3b82f6;background:rgba(59,130,246,0.06);color:#1d4ed8;' : 'border:1.5px solid var(--color-border);background:#f8fafc;color:#0f172a;'">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            Vincular a OS existente
                        </button>
                        <button @click="saidaDestino = 'nova-os'"
                                class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold text-left transition-all"
                                :style="saidaDestino === 'nova-os' ? 'border:2px solid #3b82f6;background:rgba(59,130,246,0.06);color:#1d4ed8;' : 'border:1.5px solid var(--color-border);background:#f8fafc;color:#0f172a;'">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Criar nova OS (venda avulsa)
                        </button>
                    </div>
                </div>

                <button @click="avancarSaida()"
                        :disabled="!saidaDestino"
                        class="w-full py-3.5 rounded-xl text-white text-sm font-bold transition-opacity"
                        :style="saidaDestino ? 'background:#0F172A;opacity:1;' : 'background:#94a3b8;opacity:.6;'">
                    Continuar →
                </button>
            </div>

            {{-- Step 2: vincular OS --}}
            <div x-show="saidaStep === 2 && saidaDestino === 'os'">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-display font-bold text-void text-lg">Vincular a OS</h3>
                    <button @click="saidaStep = 1" class="text-muted hover:text-void text-xs font-medium">← Voltar</button>
                </div>

                <div class="relative mb-3">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    <input type="text" x-model="buscaOS" placeholder="Buscar OS por número ou cliente…"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm focus:outline-none focus:ring-2"
                           style="border:1px solid var(--color-border);background:#f8fafc;">
                </div>

                <div class="rounded-xl overflow-hidden mb-4" style="border:1px solid var(--color-border);">
                    <template x-for="os in osFiltradas" :key="os.id">
                        <button @click="vincularOS(os)"
                                class="w-full flex items-start gap-3 px-4 py-3.5 text-left hover:bg-surface transition-colors"
                                style="border-bottom:1px solid rgba(0,0,0,0.05);">
                            <div class="flex-1 min-w-0">
                                <p class="font-mono font-semibold text-void text-sm" x-text="os.id"></p>
                                <p class="text-muted text-xs mt-0.5" x-text="os.cliente + ' · ' + os.veiculo"></p>
                            </div>
                            <svg class="w-4 h-4 text-muted flex-shrink-0 mt-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Step 2: criar nova OS --}}
            <div x-show="saidaStep === 2 && saidaDestino === 'nova-os'">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-display font-bold text-void text-lg">Nova OS</h3>
                    <button @click="saidaStep = 1" class="text-muted hover:text-void text-xs font-medium">← Voltar</button>
                </div>

                <div class="rounded-xl px-4 py-2.5 mb-4 text-xs" style="background:var(--color-surface);border:1px solid var(--color-border);">
                    <span class="text-muted">Venda avulsa · </span>
                    <span class="font-mono font-semibold text-void" x-text="pecaSelecionada?.descricao + ' × ' + saidaQtd"></span>
                    <span class="text-muted"> · </span>
                    <span class="font-mono font-semibold text-void" x-text="'R$ ' + ((pecaSelecionada?.valor_unitario ?? 0) * saidaQtd).toLocaleString('pt-BR',{minimumFractionDigits:2})"></span>
                </div>

                <label class="block text-xs text-muted uppercase tracking-wide mb-2">Cliente</label>

                <div class="relative mb-3">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    <input type="text" x-model="buscaCliente" placeholder="Buscar cliente cadastrado…"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm focus:outline-none focus:ring-2"
                           style="border:1px solid var(--color-border);background:#f8fafc;">
                </div>

                <div class="rounded-xl overflow-hidden mb-3" style="border:1px solid var(--color-border);" x-show="clientesFiltrados.length > 0">
                    <template x-for="cli in clientesFiltrados" :key="cli.id">
                        <button @click="clienteSelecionadoId = cli.id; clienteSelecionadoNome = cli.nome"
                                class="w-full flex items-center gap-3 px-4 py-3 text-left transition-colors"
                                :class="clienteSelecionadoId === cli.id ? 'bg-blue-50' : 'hover:bg-surface'"
                                style="border-bottom:1px solid rgba(0,0,0,0.05);">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold text-white" style="background:var(--color-ocean);">
                                <span x-text="cli.nome.charAt(0)"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-void text-sm" x-text="cli.nome"></p>
                                <p class="text-muted text-xs" x-text="cli.telefone"></p>
                            </div>
                            <svg x-show="clienteSelecionadoId === cli.id" class="w-4 h-4 flex-shrink-0" style="color:var(--color-spark);" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </template>
                </div>

                <div class="flex items-center gap-3 my-3">
                    <div class="flex-1 h-px" style="background:var(--color-border);"></div>
                    <span class="text-xs text-muted">ou</span>
                    <div class="flex-1 h-px" style="background:var(--color-border);"></div>
                </div>

                <button @click="alert('Fase mock — cadastro rápido de cliente')"
                        class="w-full py-2.5 rounded-xl text-sm font-semibold mb-4"
                        style="border:1.5px dashed var(--color-spark);color:var(--color-spark);background:transparent;">
                    + Cadastrar novo cliente
                </button>

                <button @click="criarOSAvulsa()"
                        :disabled="!clienteSelecionadoId"
                        class="w-full py-3.5 rounded-xl text-white text-sm font-bold transition-opacity"
                        :style="clienteSelecionadoId ? 'background:#0F172A;opacity:1;' : 'background:#94a3b8;opacity:.6;'">
                    Criar OS e registrar saída
                </button>
            </div>

        </div>
    </div>

    {{-- ==================== SHEET: NOVA PEÇA ==================== --}}
    <div x-show="sheet === 'nova-peca'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-2xl overflow-y-auto"
         style="border-top:1px solid rgba(0,0,0,0.08);max-height:90vh;">

        <div class="flex justify-center pt-3 pb-1 sticky top-0 bg-white z-10">
            <div class="w-10 h-1 rounded-full" style="background:var(--color-border);"></div>
        </div>

        <div class="px-5 pb-8 pt-2">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-display font-bold text-void text-lg">Novo Item</h3>
                <button @click="fecharSheet()" class="text-muted hover:text-void p-1 rounded-lg hover:bg-surface transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">Descrição</label>
                    <input type="text" x-model="novaPecaForm.descricao" placeholder="Ex: Pastilha de freio dianteira (par)"
                           class="w-full px-3 py-2.5 rounded-xl text-sm focus:outline-none focus:ring-2"
                           style="border:1px solid var(--color-border);background:var(--color-surface);">
                </div>

                <div>
                    <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">Categoria</label>
                    <select x-model="novaPecaForm.categoria" @change="novaPecaForm.categoria === '__nova__' ? showNovaCategoria = true : showNovaCategoria = false"
                            class="w-full px-3 py-2.5 rounded-xl text-sm focus:outline-none focus:ring-2"
                            style="border:1px solid var(--color-border);background:var(--color-surface);">
                        <option value="">Selecionar…</option>
                        <template x-for="cat in categorias" :key="cat">
                            <option :value="cat" x-text="cat"></option>
                        </template>
                        <option value="__nova__">+ Nova categoria…</option>
                    </select>
                    <div x-show="showNovaCategoria" class="mt-2">
                        <input type="text" x-model="novaCategoria" placeholder="Nome da nova categoria"
                               class="w-full px-3 py-2.5 rounded-xl text-sm focus:outline-none focus:ring-2"
                               style="border:1px solid var(--color-border);background:var(--color-surface);">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">Custo (R$)</label>
                        <input type="number" x-model="novaPecaForm.custo" placeholder="0,00"
                               class="w-full px-3 py-2.5 rounded-xl text-sm font-mono focus:outline-none focus:ring-2"
                               style="border:1px solid var(--color-border);background:var(--color-surface);">
                    </div>
                    <div>
                        <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">
                            Venda (R$)
                            <span x-show="novaPecaForm.custo && novaPecaForm.valor_unitario"
                                  class="ml-1 text-emerald-600 font-bold"
                                  x-text="'+' + Math.round(((novaPecaForm.valor_unitario - novaPecaForm.custo) / novaPecaForm.custo) * 100) + '%'">
                            </span>
                        </label>
                        <input type="number" x-model="novaPecaForm.valor_unitario" placeholder="0,00"
                               class="w-full px-3 py-2.5 rounded-xl text-sm font-mono focus:outline-none focus:ring-2"
                               style="border:1px solid var(--color-border);background:var(--color-surface);">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">Qtd inicial</label>
                        <input type="number" x-model="novaPecaForm.quantidade" placeholder="0"
                               class="w-full px-3 py-2.5 rounded-xl text-sm font-mono focus:outline-none focus:ring-2"
                               style="border:1px solid var(--color-border);background:var(--color-surface);">
                    </div>
                    <div>
                        <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">Estoque mínimo</label>
                        <input type="number" x-model="novaPecaForm.estoque_minimo" placeholder="0"
                               class="w-full px-3 py-2.5 rounded-xl text-sm font-mono focus:outline-none focus:ring-2"
                               style="border:1px solid var(--color-border);background:var(--color-surface);">
                    </div>
                </div>

                <div>
                    <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">Localização</label>
                    <input type="text" x-model="novaPecaForm.localizacao" placeholder="Ex: Prateleira A1"
                           class="w-full px-3 py-2.5 rounded-xl text-sm focus:outline-none focus:ring-2"
                           style="border:1px solid var(--color-border);background:var(--color-surface);">
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button @click="fecharSheet()"
                        class="flex-1 py-2.5 rounded-xl text-sm text-muted hover:text-void transition-colors"
                        style="border:1px solid var(--color-border);">Cancelar</button>
                <button @click="salvarNovaPeca()"
                        class="flex-1 py-2.5 rounded-xl text-white text-sm font-bold"
                        style="background:var(--color-spark);">Salvar</button>
            </div>
        </div>
    </div>

    {{-- ==================== SHEET: NOVO ORÇAMENTO ==================== --}}
    <div x-show="sheet === 'novo-orcamento'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-2xl overflow-y-auto"
         style="border-top:1px solid rgba(0,0,0,0.08);max-height:90vh;">

        <div class="flex justify-center pt-3 pb-1 sticky top-0 bg-white z-10">
            <div class="w-10 h-1 rounded-full" style="background:var(--color-border);"></div>
        </div>

        <div class="px-5 pb-8 pt-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display font-bold text-void text-lg">Novo Orçamento</h3>
                <button @click="fecharSheet()" class="text-muted hover:text-void p-1 rounded-lg hover:bg-surface transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Busca de peças --}}
            <div class="relative mb-3">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                <input type="text" x-model="buscaOrcamento" placeholder="Adicionar peça ao orçamento…"
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm focus:outline-none focus:ring-2"
                       style="border:1px solid var(--color-border);background:#f8fafc;">
            </div>

            {{-- Lista de peças para adicionar --}}
            <div class="rounded-xl overflow-hidden mb-4" style="border:1px solid var(--color-border);" x-show="buscaOrcamento.trim().length > 0">
                <template x-for="item in pecasParaOrcamento" :key="item.id">
                    <button @click="adicionarAoOrcamento(item)"
                            class="w-full flex items-center justify-between gap-3 px-4 py-3 text-left hover:bg-surface transition-colors"
                            style="border-bottom:1px solid rgba(0,0,0,0.05);">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-void text-sm truncate" x-text="item.descricao"></p>
                            <p class="text-muted text-xs" x-text="'R$ ' + item.valor_unitario.toLocaleString('pt-BR',{minimumFractionDigits:2}) + ' · ' + item.quantidade + ' em estoque'"></p>
                        </div>
                        <span class="text-spark text-xs font-semibold flex-shrink-0">+ Adicionar</span>
                    </button>
                </template>
            </div>

            {{-- Itens do orçamento --}}
            <div x-show="itensOrcamento.length > 0" class="mb-4">
                <p class="text-xs text-muted uppercase tracking-wide mb-2">Itens selecionados</p>
                <div class="rounded-xl overflow-hidden" style="border:1px solid var(--color-border);">
                    <template x-for="(item, i) in itensOrcamento" :key="i">
                        <div class="flex items-center gap-3 px-4 py-3" style="border-bottom:1px solid rgba(0,0,0,0.05);">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-void text-sm truncate" x-text="item.descricao"></p>
                                <p class="font-mono text-xs text-muted" x-text="'R$ ' + (item.valor_unitario * item.qtd).toLocaleString('pt-BR',{minimumFractionDigits:2})"></p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button @click="item.qtd = Math.max(1, item.qtd - 1)"
                                        class="w-6 h-6 rounded flex items-center justify-center" style="border:1px solid var(--color-border);">
                                    <svg class="w-3 h-3 text-muted" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                </button>
                                <span class="font-mono font-bold text-void text-sm w-5 text-center" x-text="item.qtd"></span>
                                <button @click="item.qtd++"
                                        class="w-6 h-6 rounded flex items-center justify-center text-white" style="background:var(--color-spark);">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                </button>
                                <button @click="itensOrcamento.splice(i,1)" class="ml-1 text-red-400 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                    <div class="flex items-center justify-between px-4 py-3" style="background:var(--color-surface);">
                        <span class="text-sm font-semibold text-void">Total</span>
                        <span class="font-mono font-bold text-void" x-text="'R$ ' + totalOrcamento.toLocaleString('pt-BR',{minimumFractionDigits:2})"></span>
                    </div>
                </div>
            </div>

            {{-- Destino --}}
            <div x-show="itensOrcamento.length > 0" class="space-y-2.5 mb-5">
                <p class="text-xs text-muted uppercase tracking-wide">Destino</p>
                <button @click="orcDestino = 'os'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-left transition-all"
                        :style="orcDestino === 'os' ? 'border:2px solid #3b82f6;background:rgba(59,130,246,0.06);color:#1d4ed8;' : 'border:1.5px solid var(--color-border);background:#f8fafc;color:#0f172a;'">
                    🔗 Vincular a OS existente
                </button>
                <button @click="orcDestino = 'pdf'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-left transition-all"
                        :style="orcDestino === 'pdf' ? 'border:2px solid #3b82f6;background:rgba(59,130,246,0.06);color:#1d4ed8;' : 'border:1.5px solid var(--color-border);background:#f8fafc;color:#0f172a;'">
                    📄 Salvar como PDF (avulso)
                </button>
            </div>

            <button x-show="itensOrcamento.length > 0"
                    @click="salvarOrcamento()"
                    :disabled="!orcDestino"
                    class="w-full py-3.5 rounded-xl text-white text-sm font-bold transition-opacity"
                    :style="orcDestino ? 'background:#0F172A;opacity:1;' : 'background:#94a3b8;opacity:.6;'">
                Salvar Orçamento
            </button>
        </div>
    </div>

</div>

<script>
function estoquePage() {
    return {
        // --- dados ---
        itens:      [],
        metricas:   {},
        categorias: [],
        historico: [
            { tipo: 'entrada', descricao: 'Pastilha de freio dianteira', qtd: 4,  data: '20/06/2026', origem: 'Compra — Fornecedor ABC' },
            { tipo: 'saida',   descricao: 'Óleo motor 5W30',             qtd: 2,  data: '19/06/2026', origem: 'OS-2024-017' },
            { tipo: 'saida',   descricao: 'Filtro de óleo universal',    qtd: 1,  data: '18/06/2026', origem: 'OS-2024-016' },
            { tipo: 'ajuste',  descricao: 'Balanço físico geral',         qtd: 0,  data: '15/06/2026', origem: 'Contagem' },
            { tipo: 'saida',   descricao: 'Vela de ignição NGK',         qtd: 3,  data: '14/06/2026', origem: 'OS-2024-015' },
        ],
        orcamentos: [
            { id: 'ORC-001', total: 340.00, status: 'vinculado', os: 'OS-2024-018', data: '19/06/2026' },
            { id: 'ORC-002', total: 185.00, status: 'avulso',    data: '22/06/2026' },
            { id: 'ORC-003', total: 520.00, status: 'avulso',    data: '23/06/2026' },
        ],
        osAbertas: [
            { id: 'OS-2024-018', cliente: 'João Silva',    veiculo: 'Honda Civic' },
            { id: 'OS-2024-019', cliente: 'Maria Oliveira', veiculo: 'Toyota Corolla' },
            { id: 'OS-2024-020', cliente: 'Carlos Santos',  veiculo: 'VW Polo' },
        ],
        clientes: [
            { id: 1, nome: 'João Silva',     telefone: '(11) 99999-1111' },
            { id: 2, nome: 'Maria Oliveira', telefone: '(11) 99999-2222' },
            { id: 3, nome: 'Carlos Santos',  telefone: '(11) 99999-3333' },
        ],

        // --- UI state ---
        tab:             'pecas',
        subtabBalanco:   'contagem',
        busca:           '',
        categoriaAtiva:  'todos',
        filtroStatus:    'todos',

        // --- sheet state ---
        sheet:                null,
        pecaSelecionada:       null,
        entradaQtd:            1,
        entradaFornecedor:     '',
        saidaQtd:              1,
        saidaStep:             1,
        saidaDestino:          null,
        buscaOS:               '',
        buscaCliente:          '',
        clienteSelecionadoId:  null,
        clienteSelecionadoNome:'',

        // --- balanco ---
        contagem: {},

        // --- orcamento ---
        itensOrcamento: [],
        buscaOrcamento: '',
        orcDestino:     null,

        // --- nova peca ---
        novaPecaForm:       { descricao:'', categoria:'', custo:'', valor_unitario:'', estoque_minimo:'', localizacao:'', quantidade:'' },
        novaCategoria:      '',
        showNovaCategoria:  false,

        // ==================== INIT ====================
        init() {
            this.itens      = window.__itens      || [];
            this.metricas   = window.__metricas   || {};
            this.categorias = window.__categorias || [];
            this.itens.forEach(i => { this.contagem[i.id] = i.quantidade; });
        },

        // ==================== COMPUTED ====================
        get itensFiltrados() {
            let lista = this.itens;
            if (this.categoriaAtiva !== 'todos') lista = lista.filter(i => i.categoria === this.categoriaAtiva);
            if (this.filtroStatus   !== 'todos') lista = lista.filter(i => i.status === this.filtroStatus);
            if (this.busca.trim())               lista = lista.filter(i => i.descricao.toLowerCase().includes(this.busca.toLowerCase()));
            return lista;
        },
        get osFiltradas() {
            if (!this.buscaOS.trim()) return this.osAbertas;
            const q = this.buscaOS.toLowerCase();
            return this.osAbertas.filter(o => o.id.toLowerCase().includes(q) || o.cliente.toLowerCase().includes(q));
        },
        get clientesFiltrados() {
            if (!this.buscaCliente.trim()) return this.clientes;
            const q = this.buscaCliente.toLowerCase();
            return this.clientes.filter(c => c.nome.toLowerCase().includes(q));
        },
        get pecasParaOrcamento() {
            if (!this.buscaOrcamento.trim()) return [];
            const q = this.buscaOrcamento.toLowerCase();
            return this.itens.filter(i => i.descricao.toLowerCase().includes(q) && i.quantidade > 0);
        },
        get totalOrcamento() {
            return this.itensOrcamento.reduce((s, i) => s + (i.valor_unitario * i.qtd), 0);
        },

        // ==================== HELPERS ====================
        margem(item) {
            if (!item || !item.custo || item.custo === 0) return 0;
            return Math.round(((item.valor_unitario - item.custo) / item.custo) * 100);
        },
        corQtd(item) {
            if (!item) return '#0F172A';
            if (item.status === 'sem_estoque') return '#DC2626';
            if (item.status === 'baixo')       return '#D97706';
            return '#0F172A';
        },
        corFundoCard(item) {
            if (!item) return '#fff';
            if (item.status === 'sem_estoque') return 'rgba(239,68,68,0.03)';
            if (item.status === 'baixo')       return 'rgba(245,158,11,0.03)';
            return '#fff';
        },
        corBadgeBg(item) {
            if (!item) return 'rgba(16,185,129,0.1)';
            if (item.status === 'sem_estoque') return 'rgba(239,68,68,0.1)';
            if (item.status === 'baixo')       return 'rgba(245,158,11,0.1)';
            return 'rgba(16,185,129,0.1)';
        },
        labelStatus(item) {
            if (!item) return '';
            if (item.status === 'sem_estoque') return 'Zerado';
            if (item.status === 'baixo')       return 'Baixo';
            return 'OK';
        },
        contemDivergencia(item) {
            return (this.contagem[item.id] ?? item.quantidade) !== item.quantidade;
        },

        // ==================== SHEET ACTIONS ====================
        abrirSheet(nome) {
            this.sheet = nome;
        },
        fecharSheet() {
            this.sheet               = null;
            this.pecaSelecionada     = null;
            this.saidaStep           = 1;
            this.saidaDestino        = null;
            this.buscaOS             = '';
            this.buscaCliente        = '';
            this.clienteSelecionadoId   = null;
            this.clienteSelecionadoNome = '';
            this.buscaOrcamento      = '';
            this.orcDestino          = null;
        },
        abrirDetalhe(item) {
            this.pecaSelecionada = item;
            this.sheet           = 'detalhe';
        },
        iniciarEntrada() {
            this.entradaQtd       = 1;
            this.entradaFornecedor= '';
            this.sheet            = 'entrada';
        },
        iniciarSaida() {
            this.saidaQtd    = 1;
            this.saidaStep   = 1;
            this.saidaDestino= null;
            this.sheet       = 'saida';
        },
        avancarSaida() {
            if (!this.saidaDestino) return;
            this.saidaStep = 2;
        },
        confirmarEntrada() {
            const item = this.itens.find(i => i.id === this.pecaSelecionada.id);
            if (item) {
                item.quantidade += this.entradaQtd;
                item.status = item.quantidade <= 0 ? 'sem_estoque' : (item.quantidade <= item.estoque_minimo ? 'baixo' : 'ok');
                this.contagem[item.id] = item.quantidade;
            }
            this.historico.unshift({ tipo: 'entrada', descricao: this.pecaSelecionada.descricao, qtd: this.entradaQtd, data: new Date().toLocaleDateString('pt-BR'), origem: this.entradaFornecedor || 'Entrada manual' });
            this.recalcularMetricas();
            this.fecharSheet();
        },
        vincularOS(os) {
            const item = this.itens.find(i => i.id === this.pecaSelecionada.id);
            if (item) {
                item.quantidade = Math.max(0, item.quantidade - this.saidaQtd);
                item.status = item.quantidade <= 0 ? 'sem_estoque' : (item.quantidade <= item.estoque_minimo ? 'baixo' : 'ok');
                this.contagem[item.id] = item.quantidade;
            }
            this.historico.unshift({ tipo: 'saida', descricao: this.pecaSelecionada.descricao, qtd: this.saidaQtd, data: new Date().toLocaleDateString('pt-BR'), origem: os.id });
            this.recalcularMetricas();
            this.fecharSheet();
            alert('Peça vinculada à ' + os.id + ' com sucesso!');
        },
        criarOSAvulsa() {
            if (!this.clienteSelecionadoId) return;
            const item = this.itens.find(i => i.id === this.pecaSelecionada.id);
            if (item) {
                item.quantidade = Math.max(0, item.quantidade - this.saidaQtd);
                item.status = item.quantidade <= 0 ? 'sem_estoque' : (item.quantidade <= item.estoque_minimo ? 'baixo' : 'ok');
                this.contagem[item.id] = item.quantidade;
            }
            const novaOSId = 'OS-2024-' + (parseInt(this.osAbertas[0]?.id.split('-')[2] || '020') + 1);
            this.historico.unshift({ tipo: 'saida', descricao: this.pecaSelecionada.descricao, qtd: this.saidaQtd, data: new Date().toLocaleDateString('pt-BR'), origem: novaOSId });
            this.recalcularMetricas();
            this.fecharSheet();
            alert('OS ' + novaOSId + ' criada para ' + this.clienteSelecionadoNome + '!');
        },
        confirmarBalanco() {
            let divergencias = 0;
            this.itens.forEach(item => {
                const qtdReal = this.contagem[item.id] ?? item.quantidade;
                if (qtdReal !== item.quantidade) {
                    divergencias++;
                    item.quantidade = qtdReal;
                    item.status = item.quantidade <= 0 ? 'sem_estoque' : (item.quantidade <= item.estoque_minimo ? 'baixo' : 'ok');
                }
            });
            if (divergencias > 0) {
                this.historico.unshift({ tipo: 'ajuste', descricao: 'Balanço físico — ' + divergencias + ' ajuste(s)', qtd: divergencias, data: new Date().toLocaleDateString('pt-BR'), origem: 'Contagem' });
            }
            this.recalcularMetricas();
            alert(divergencias > 0 ? 'Balanço confirmado. ' + divergencias + ' item(ns) ajustado(s).' : 'Balanço confirmado. Nenhuma divergência.');
        },
        salvarNovaPeca() {
            const f = this.novaPecaForm;
            if (!f.descricao || !f.valor_unitario) { alert('Preencha ao menos descrição e preço de venda.'); return; }
            const cat = this.showNovaCategoria && this.novaCategoria ? this.novaCategoria : f.categoria;
            if (cat && !this.categorias.includes(cat)) this.categorias.push(cat);
            const novoItem = {
                id:             Date.now(),
                descricao:      f.descricao,
                categoria:      cat,
                custo:          parseFloat(f.custo) || 0,
                valor_unitario: parseFloat(f.valor_unitario) || 0,
                quantidade:     parseInt(f.quantidade) || 0,
                estoque_minimo: parseInt(f.estoque_minimo) || 0,
                localizacao:    f.localizacao,
                status:         'ok',
            };
            novoItem.status = novoItem.quantidade <= 0 ? 'sem_estoque' : (novoItem.quantidade <= novoItem.estoque_minimo ? 'baixo' : 'ok');
            this.itens.push(novoItem);
            this.contagem[novoItem.id] = novoItem.quantidade;
            this.recalcularMetricas();
            this.novaPecaForm = { descricao:'', categoria:'', custo:'', valor_unitario:'', estoque_minimo:'', localizacao:'', quantidade:'' };
            this.novaCategoria = '';
            this.showNovaCategoria = false;
            this.fecharSheet();
        },
        adicionarAoOrcamento(item) {
            const existente = this.itensOrcamento.find(i => i.id === item.id);
            if (existente) { existente.qtd++; }
            else            { this.itensOrcamento.push({ ...item, qtd: 1 }); }
            this.buscaOrcamento = '';
        },
        salvarOrcamento() {
            if (!this.orcDestino || this.itensOrcamento.length === 0) return;
            const novoId = 'ORC-' + String(this.orcamentos.length + 1).padStart(3, '0');
            this.orcamentos.unshift({
                id:     novoId,
                total:  this.totalOrcamento,
                status: this.orcDestino === 'os' ? 'vinculado' : 'avulso',
                os:     this.orcDestino === 'os' ? 'OS-2024-018' : undefined,
                data:   new Date().toLocaleDateString('pt-BR'),
            });
            this.itensOrcamento = [];
            this.fecharSheet();
            alert(novoId + ' salvo com sucesso!');
        },
        fabAcao() {
            if (this.tab === 'pecas')       this.abrirSheet('nova-peca');
            if (this.tab === 'orcamentos')  this.abrirSheet('novo-orcamento');
            if (this.tab === 'balanco')     this.subtabBalanco = 'contagem';
        },
        recalcularMetricas() {
            this.metricas.total       = this.itens.length;
            this.metricas.baixo       = this.itens.filter(i => i.status === 'baixo').length;
            this.metricas.sem_estoque = this.itens.filter(i => i.status === 'sem_estoque').length;
            this.metricas.valor_total = this.itens.reduce((s, i) => s + (i.quantidade * i.valor_unitario), 0);
        },
    };
}
</script>

</x-layouts.oficina>
