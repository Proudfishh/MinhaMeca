<x-layouts.oficina title="Orçamentos">

<script>
window.__orcamentos = {!! json_encode(
    collect($orcamentos)->sortByDesc('criado_em')->values(),
    JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
) !!};
</script>

<div x-data="orcamentosIndex()" x-init="init()">

{{-- Header --}}
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-2 flex-wrap">
        <h2 class="font-display font-bold text-void text-xl">Orçamentos</h2>
        <span class="font-mono text-xs px-2 py-0.5 rounded-full font-semibold"
              style="background:rgba(59,130,246,0.1);color:#3B82F6;"
              x-text="temFiltro ? (filtrados.length + ' de {{ $metricas['total'] }}') : (filtrados.length + ' total')"></span>
        <span x-show="pendentesFiltrado > 0" x-cloak
              class="font-mono text-xs px-2 py-0.5 rounded-full font-semibold"
              style="background:rgba(245,158,11,0.12);color:#D97706;"
              x-text="pendentesFiltrado + ' pendente(s)'"></span>
    </div>
    <a href="{{ route('oficina.orcamentos.create') }}"
       class="hidden md:inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-white text-sm font-medium"
       style="background:var(--color-spark);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Novo Orçamento
    </a>
</div>

{{-- Métricas --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    <div class="bg-white rounded-xl px-4 py-3" style="border:1px solid var(--color-border);box-shadow:0 1px 4px rgba(0,0,0,0.04);">
        <p class="text-muted text-[10px] font-semibold uppercase tracking-wide mb-1">Total</p>
        <p class="font-mono font-bold text-void text-lg" x-text="filtrados.length">{{ $metricas['total'] }}</p>
    </div>
    <div class="bg-white rounded-xl px-4 py-3" style="border:1px solid var(--color-border);box-shadow:0 1px 4px rgba(0,0,0,0.04);">
        <p class="text-muted text-[10px] font-semibold uppercase tracking-wide mb-1">Pendentes</p>
        <p class="font-mono font-bold text-lg" :class="pendentesFiltrado > 0 ? 'text-amber-600' : 'text-void'" x-text="pendentesFiltrado">{{ $metricas['pendente'] }}</p>
    </div>
    <div class="bg-white rounded-xl px-4 py-3" style="border:1px solid var(--color-border);box-shadow:0 1px 4px rgba(0,0,0,0.04);">
        <p class="text-muted text-[10px] font-semibold uppercase tracking-wide mb-1">Aprovados</p>
        <p class="font-mono font-bold text-emerald-600 text-lg" x-text="aprovadosFiltrado">{{ $metricas['aprovado'] }}</p>
    </div>
    <div class="bg-white rounded-xl px-4 py-3" style="border:1px solid var(--color-border);box-shadow:0 1px 4px rgba(0,0,0,0.04);">
        <p class="text-muted text-[10px] font-semibold uppercase tracking-wide mb-1">Valor total</p>
        <p class="font-mono font-bold text-void text-lg" x-text="'R$ ' + formatarValorInt(valorFiltrado)">R$ {{ number_format($metricas['valor'], 0, ',', '.') }}</p>
    </div>
</div>

@if(session('sucesso'))
<div class="flex items-center gap-2 px-4 py-3 rounded-xl mb-4 text-sm font-medium"
     style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);color:#059669;">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('sucesso') }}
</div>
@endif

{{-- Busca --}}
<div class="relative mb-4">
    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
         style="color:#94a3b8;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
    </svg>
    <input type="text" x-model="busca"
           placeholder="Buscar por cliente, placa ou veículo…"
           class="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm text-void placeholder-muted bg-white focus:outline-none focus:ring-2 transition-colors"
           style="border:1px solid var(--color-border);">
    <button x-show="busca.length > 0" @click="busca = ''"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-void transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

{{-- Chips de status + botão Filtros --}}
<div class="flex items-center gap-2 mb-4 flex-wrap">
    <button @click="statusAtivo = 'todos'" :style="chipStyle('todos')"
            class="text-xs font-semibold px-3 py-1.5 rounded-full transition-colors">Todos</button>
    <button @click="statusAtivo = 'rascunho'" :style="chipStyle('rascunho')"
            class="text-xs font-semibold px-3 py-1.5 rounded-full transition-colors">Rascunho</button>
    <button @click="statusAtivo = 'pendente'" :style="chipStyle('pendente')"
            class="text-xs font-semibold px-3 py-1.5 rounded-full transition-colors">Pendente</button>
    <button @click="statusAtivo = 'aprovado'" :style="chipStyle('aprovado')"
            class="text-xs font-semibold px-3 py-1.5 rounded-full transition-colors">Aprovado</button>

    {{-- Botão Filtros + painel (dropdown no desktop, bottom sheet no mobile) --}}
    <div class="relative ml-auto">
        <button @click="painelAberto ? painelAberto = false : abrirPainel()"
                class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full transition-colors"
                style="background:rgba(59,130,246,0.08);color:#3B82F6;border:1px solid rgba(59,130,246,0.2);">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 12.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-8.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filtros
            <span x-show="filtrosAtivosCount > 0" x-text="filtrosAtivosCount"
                  class="text-[9px] font-bold text-white rounded-full px-1.5 leading-tight" style="background:#3B82F6;"></span>
            <svg class="w-3 h-3 transition-transform" :class="painelAberto ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Backdrop (só mobile) --}}
        <div x-show="painelAberto" @click="painelAberto = false"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 md:hidden" style="background:rgba(0,0,0,0.5);" x-cloak></div>

        {{-- Painel --}}
        <div x-show="painelAberto" @click.outside="painelAberto = false" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-y-full md:translate-y-0 md:opacity-0 md:-translate-y-1"
             x-transition:enter-end="translate-y-0 md:opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-y-0 md:opacity-100"
             x-transition:leave-end="translate-y-full md:translate-y-0 md:opacity-0"
             class="fixed inset-x-0 bottom-0 z-50 rounded-t-2xl px-4 pb-6 pt-3
                    md:absolute md:inset-x-auto md:right-0 md:left-auto md:bottom-auto md:top-[calc(100%+0.5rem)] md:w-80 md:rounded-xl md:p-4 md:pb-4"
             style="background:#fff;border:1px solid var(--color-border);box-shadow:0 -8px 30px rgba(0,0,0,0.15);max-height:85vh;overflow-y:auto;">

            <div class="md:hidden flex justify-center mb-3">
                <div class="w-10 h-1 rounded-full" style="background:rgba(0,0,0,0.1);"></div>
            </div>
            <div class="flex items-center justify-between mb-4">
                <p class="font-bold text-void text-base">Filtros</p>
                <button @click="limparDraft()" class="text-xs text-muted hover:text-void transition-colors">Limpar</button>
            </div>

            {{-- Validade --}}
            <div class="mb-4">
                <p class="text-[10px] font-bold text-muted uppercase tracking-wide mb-2">Validade</p>
                <template x-for="op in [['todas','Todas'],['vencendo','Vencendo (7d)'],['vencidos','Vencidos'],['mes','Este mês']]" :key="op[0]">
                    <button @click="draft.validade = op[0]" :style="pillStyle('validade', op[0])"
                            class="text-xs px-3 py-1.5 rounded-lg mr-1.5 mb-1.5" x-text="op[1]"></button>
                </template>
            </div>

            {{-- Faixa de valor --}}
            <div class="mb-4">
                <p class="text-[10px] font-bold text-muted uppercase tracking-wide mb-2">Faixa de valor</p>
                <template x-for="op in [['qualquer','Qualquer'],['ate200','Até R$ 200'],['200a500','R$ 200–500'],['500mais','R$ 500+']]" :key="op[0]">
                    <button @click="draft.valor = op[0]" :style="pillStyle('valor', op[0])"
                            class="text-xs px-3 py-1.5 rounded-lg mr-1.5 mb-1.5" x-text="op[1]"></button>
                </template>
            </div>

            {{-- Vínculo com OS --}}
            <div class="mb-4">
                <p class="text-[10px] font-bold text-muted uppercase tracking-wide mb-2">Vínculo com OS</p>
                <template x-for="op in [['todos','Todos'],['com','Com OS'],['sem','Avulso']]" :key="op[0]">
                    <button @click="draft.vinculo = op[0]" :style="pillStyle('vinculo', op[0])"
                            class="text-xs px-3 py-1.5 rounded-lg mr-1.5 mb-1.5" x-text="op[1]"></button>
                </template>
            </div>

            {{-- Período de criação --}}
            <div class="mb-4">
                <p class="text-[10px] font-bold text-muted uppercase tracking-wide mb-2">Período de criação</p>
                <template x-for="op in [['qualquer','Qualquer'],['7dias','Últimos 7 dias'],['mes','Este mês']]" :key="op[0]">
                    <button @click="draft.periodo = op[0]" :style="pillStyle('periodo', op[0])"
                            class="text-xs px-3 py-1.5 rounded-lg mr-1.5 mb-1.5" x-text="op[1]"></button>
                </template>
            </div>

            {{-- Ordenar por --}}
            <div class="mb-5">
                <p class="text-[10px] font-bold text-muted uppercase tracking-wide mb-2">Ordenar por</p>
                <template x-for="op in [['recentes','Mais recentes'],['valor','Maior valor'],['validade','Validade próxima']]" :key="op[0]">
                    <button @click="draft.ordenar = op[0]" :style="pillStyle('ordenar', op[0])"
                            class="text-xs px-3 py-1.5 rounded-lg mr-1.5 mb-1.5" x-text="op[1]"></button>
                </template>
            </div>

            <button @click="aplicar()" class="w-full py-3 rounded-xl text-sm font-bold text-white" style="background:#0f172a;"
                    x-text="'Aplicar (' + previaCount + ')'"></button>
        </div>
    </div>
</div>

{{-- Estado vazio --}}
<template x-if="orcamentos.length === 0">
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3"
             style="background:var(--color-surface);border:1px solid var(--color-border);">
            <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p class="font-semibold text-void text-sm mb-1">Nenhum orçamento criado.</p>
        <a href="{{ route('oficina.orcamentos.create') }}" class="text-spark text-xs font-medium hover:underline">Criar primeiro orçamento →</a>
    </div>
</template>

<template x-if="orcamentos.length > 0">
    <div>

        {{-- Resultado vazio (busca ou filtros) --}}
        <template x-if="filtrados.length === 0">
            <div class="flex flex-col items-center justify-center py-14 text-center">
                <p class="text-void text-sm font-semibold mb-1">Nenhum orçamento com esses filtros</p>
                <p class="text-muted text-xs mb-3">Ajuste a busca, o status ou os filtros avançados</p>
                <button @click="limparTudo()" class="text-spark text-xs font-semibold hover:underline">Limpar tudo</button>
            </div>
        </template>

        {{-- MOBILE: cards com swipe-to-delete --}}
        <div x-show="filtrados.length > 0" class="md:hidden rounded-2xl overflow-hidden mb-20" style="border:1px solid rgba(0,0,0,0.06);box-shadow:0 2px 8px rgba(0,0,0,0.04);">
            <template x-for="(orc, i) in filtrados" :key="orc.id">
                {{-- Trilho: fundo vermelho fixo + card deslizante --}}
                <div class="relative overflow-hidden bg-white"
                     :style="(i < filtrados.length - 1 ? 'border-bottom:1px solid rgba(0,0,0,0.05);' : '') + (saindoId === orc.id ? 'max-height:0;opacity:0;transition:all 0.26s ease;' : 'max-height:220px;')">

                    {{-- Fundo: botão excluir revelado ao deslizar --}}
                    <button type="button" @click="abrirSheetExcluir(orc.id)"
                            class="absolute inset-y-0 right-0 flex flex-col items-center justify-center gap-0.5 text-white"
                            style="width:88px;background:#ef4444;"
                            :tabindex="swipeId === orc.id ? 0 : -1"
                            aria-label="Excluir orçamento">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span class="text-[10px] font-semibold">Excluir</span>
                    </button>

                    {{-- Card deslizante --}}
                    <a :href="'{{ url('oficina/orcamentos') }}/' + orc.id"
                       @touchstart.passive="onTouchStart(orc, $event)"
                       @touchmove="onTouchMove(orc, $event)"
                       @touchend.passive="onTouchEnd(orc, $event)"
                       @click="onCardClick(orc, $event)"
                       class="relative flex items-center gap-3 px-4 py-4 bg-white active:bg-slate-50"
                       :style="'transform:translateX(' + offsetDe(orc.id) + 'px);' + (arrastando === orc.id ? '' : 'transition:transform 0.2s ease;')">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono font-bold text-void text-sm" x-text="orc.codigo"></span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold"
                                      :style="statusInfo(orc.status)">
                                    <span x-text="statusLabel(orc.status)"></span>
                                </span>
                            </div>
                            <p class="text-muted text-xs truncate">
                                <span x-text="orc.cliente || 'Sem cliente'"></span>
                                <template x-if="orc.veiculo">
                                    <span> · <span x-text="orc.veiculo"></span></span>
                                </template>
                            </p>
                            <template x-if="orc.placa">
                                <p class="text-[10px] font-mono mt-0.5 text-muted" x-text="orc.placa"></p>
                            </template>
                            <template x-if="orc.os_vinculada">
                                <p class="text-[10px] mt-0.5" style="color:#3B82F6;">
                                    🔗 <span x-text="orc.os_vinculada"></span>
                                </p>
                            </template>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-mono font-bold text-void text-base"
                               x-text="'R$ ' + formatarValor(orc.total)"></p>
                            <p class="text-[10px] text-muted mt-0.5"
                               x-text="'Válido até ' + formatarDataCurta(orc.validade)"></p>
                        </div>
                        <svg class="w-4 h-4 flex-shrink-0 ml-1" style="color:#CBD5E1;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                        </svg>
                    </a>
                </div>
            </template>
        </div>

        {{-- DESKTOP: tabela --}}
        <div x-show="filtrados.length > 0" class="hidden md:block bg-white rounded-xl overflow-hidden" style="border:1px solid var(--color-border);">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--color-border);">
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Código</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Cliente</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Veículo / Placa</th>
                        <th class="text-right px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Total</th>
                        <th class="text-center px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Validade</th>
                        <th class="text-center px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Status</th>
                        <th class="text-center px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">OS</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="orc in filtrados" :key="orc.id">
                        <tr class="hover:bg-surface transition-colors" style="border-bottom:1px solid var(--color-border);">
                            <td class="px-5 py-3.5">
                                <span class="font-mono font-semibold text-void" x-text="orc.codigo"></span>
                            </td>
                            <td class="px-5 py-3.5 text-void" x-text="orc.cliente || '—'"></td>
                            <td class="px-5 py-3.5">
                                <span class="text-void text-sm" x-text="orc.veiculo || '—'"></span>
                                <template x-if="orc.placa">
                                    <span class="font-mono text-xs text-muted ml-1.5" x-text="orc.placa"></span>
                                </template>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="font-mono font-semibold text-void"
                                      x-text="'R$ ' + formatarValor(orc.total)"></span>
                            </td>
                            <td class="px-5 py-3.5 text-center text-muted text-xs"
                                x-text="formatarDataLonga(orc.validade)"></td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                      :style="statusInfo(orc.status)"
                                      x-text="statusLabel(orc.status)"></span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <template x-if="orc.os_vinculada">
                                    <span class="text-xs font-medium" style="color:#3B82F6;" x-text="orc.os_vinculada"></span>
                                </template>
                                <template x-if="!orc.os_vinculada">
                                    <span class="text-muted text-xs">—</span>
                                </template>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <a :href="'{{ url('oficina/orcamentos') }}/' + orc.id"
                                   class="text-spark text-xs font-medium hover:underline">Ver</a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

    </div>
</template>

{{-- ============================================================
     BOTTOM SHEET — CONFIRMAR EXCLUSÃO (swipe)
============================================================ --}}
<div x-show="sheetExcluirId !== null"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="fecharSheetExcluir()"
     class="fixed inset-0 z-40 md:hidden" style="background:rgba(0,0,0,0.5);" x-cloak></div>
<div x-show="sheetExcluirId !== null"
     x-transition:enter="transition ease-out duration-250" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
     @click.stop
     class="fixed bottom-0 left-0 right-0 z-50 rounded-t-2xl px-4 pb-8 pt-3 md:hidden" style="background:#fff;" x-cloak>

    <div class="flex justify-center mb-4">
        <div class="w-10 h-1 rounded-full" style="background:rgba(0,0,0,0.1);"></div>
    </div>

    <div class="flex flex-col items-center text-center mb-6">
        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3" style="background:rgba(239,68,68,0.1);">
            <svg class="w-6 h-6" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
        <p class="font-bold text-void text-base">Excluir <span x-text="codigoSheet()"></span>?</p>
        <p class="text-sm text-muted mt-1">Esta ação não pode ser desfeita. O orçamento será removido permanentemente.</p>
    </div>

    <div class="flex gap-2">
        <button @click="fecharSheetExcluir()"
                class="flex-1 py-3 rounded-xl text-sm font-semibold text-void transition-colors"
                style="border:1px solid var(--color-border);background:var(--color-surface);">
            Cancelar
        </button>
        <button @click="confirmarExcluir()"
                class="flex-1 py-3 rounded-xl text-sm font-bold text-white" style="background:#ef4444;">
            Excluir
        </button>
    </div>
</div>

{{-- Toast --}}
<div x-show="toastVisivel" x-cloak
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed left-1/2 -translate-x-1/2 z-[60] flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-medium"
     style="bottom:5rem;background:#0f172a;box-shadow:0 6px 20px rgba(0,0,0,0.25);">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    <span x-text="toastMsg"></span>
</div>

</div>{{-- /x-data --}}

{{-- FAB mobile --}}
<a href="{{ route('oficina.orcamentos.create') }}"
   class="md:hidden fixed right-4 z-30 w-14 h-14 rounded-full text-white flex items-center justify-center shadow-lg"
   style="bottom:5rem;background:var(--color-spark);box-shadow:0 4px 16px rgba(59,130,246,0.45);">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
    </svg>
</a>

<script>
function orcamentosIndex() {
    const padroes = () => ({ validade: 'todas', valor: 'qualquer', vinculo: 'todos', periodo: 'qualquer', ordenar: 'recentes' });

    return {
        busca: '',
        orcamentos: [],

        statusAtivo: 'todos',
        painelAberto: false,
        ativo: padroes(),
        draft: padroes(),

        // ── Swipe-to-delete (mobile) ──
        REVEAL: 88,          // largura do botão excluir
        THRESHOLD: 40,       // distância p/ travar aberto
        swipeId: null,       // card revelado
        arrastando: null,    // card em arrasto ativo (desativa transição)
        swipeStartX: 0,
        swipeStartY: 0,
        swipeDx: 0,
        eixo: null,          // 'h' | 'v' — eixo dominante travado
        saindoId: null,      // card em animação de saída
        sheetExcluirId: null,
        toastVisivel: false,
        toastMsg: '',
        _toastT: null,

        init() {
            this.orcamentos = window.__orcamentos || [];
        },

        // ── Swipe ──
        offsetDe(id) {
            if (this.arrastando === id) return this.swipeDx;
            return this.swipeId === id ? -this.REVEAL : 0;
        },
        onTouchStart(orc, e) {
            if (this.swipeId && this.swipeId !== orc.id) this.swipeId = null;
            const t = e.touches[0];
            this.swipeStartX = t.clientX;
            this.swipeStartY = t.clientY;
            this.eixo = null;
            this.arrastando = null;
            this.swipeDx = this.swipeId === orc.id ? -this.REVEAL : 0;
        },
        onTouchMove(orc, e) {
            const t = e.touches[0];
            const dx = t.clientX - this.swipeStartX;
            const dy = t.clientY - this.swipeStartY;
            if (!this.eixo) {
                if (Math.abs(dx) < 6 && Math.abs(dy) < 6) return;
                this.eixo = Math.abs(dx) > Math.abs(dy) ? 'h' : 'v';
                if (this.eixo === 'h') this.arrastando = orc.id;
            }
            if (this.eixo !== 'h') return;   // vertical → deixa rolar
            e.preventDefault();
            const base = this.swipeId === orc.id ? -this.REVEAL : 0;
            this.swipeDx = Math.max(-this.REVEAL, Math.min(0, base + dx));
        },
        onTouchEnd(orc, e) {
            if (this.eixo === 'h') {
                this.swipeId = this.swipeDx <= -this.THRESHOLD ? orc.id : null;
            }
            this.arrastando = null;
            this.eixo = null;
        },
        onCardClick(orc, e) {
            // Card aberto: primeiro toque só fecha, não navega
            if (this.swipeId === orc.id) {
                e.preventDefault();
                this.swipeId = null;
            }
        },

        // ── Exclusão ──
        abrirSheetExcluir(id) { this.sheetExcluirId = id; },
        fecharSheetExcluir() { this.sheetExcluirId = null; },
        codigoSheet() {
            const o = this.orcamentos.find(x => x.id === this.sheetExcluirId);
            return o ? o.codigo : '';
        },
        confirmarExcluir() {
            const id = this.sheetExcluirId;
            this.sheetExcluirId = null;
            this.swipeId = null;
            this.saindoId = id;
            // TODO backend real: chamar rota de exclusão antes de remover
            setTimeout(() => {
                this.orcamentos = this.orcamentos.filter(o => o.id !== id);
                this.saindoId = null;
                this.mostrarToast('Orçamento excluído');
            }, 260);
        },
        mostrarToast(msg) {
            this.toastMsg = msg;
            this.toastVisivel = true;
            clearTimeout(this._toastT);
            this._toastT = setTimeout(() => { this.toastVisivel = false; }, 3000);
        },

        normalizar(s) {
            return (s || '').toLowerCase().normalize('NFD').replace(/\p{M}/gu, '');
        },

        // ── Helpers de data ──
        fmtData(d) {
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        },
        hojeStr() { return this.fmtData(new Date()); },
        addDiasStr(n) { const d = new Date(); d.setDate(d.getDate() + n); return this.fmtData(d); },

        // ── Predicados de filtro ──
        matchValidade(o, v) {
            if (v === 'todas') return true;
            if (!o.validade) return false;
            const hoje = this.hojeStr();
            if (v === 'vencidos') return o.validade < hoje;
            if (v === 'vencendo') return o.validade >= hoje && o.validade <= this.addDiasStr(7);
            if (v === 'mes') return o.validade.slice(0, 7) === hoje.slice(0, 7);
            return true;
        },
        matchValor(o, v) {
            const t = parseFloat(o.total || 0);
            if (v === 'ate200') return t <= 200;
            if (v === '200a500') return t > 200 && t <= 500;
            if (v === '500mais') return t > 500;
            return true;
        },
        matchVinculo(o, v) {
            if (v === 'todos') return true;
            return v === 'com' ? !!o.os_vinculada : !o.os_vinculada;
        },
        matchPeriodo(o, v) {
            if (v === 'qualquer') return true;
            if (!o.criado_em) return false;
            if (v === '7dias') return o.criado_em >= this.addDiasStr(-7);
            if (v === 'mes') return o.criado_em.slice(0, 7) === this.hojeStr().slice(0, 7);
            return true;
        },
        ordenarLista(lista, ord) {
            const arr = [...lista];
            if (ord === 'valor') arr.sort((a, b) => parseFloat(b.total || 0) - parseFloat(a.total || 0));
            else if (ord === 'validade') arr.sort((a, b) => (a.validade || '9999-99-99').localeCompare(b.validade || '9999-99-99'));
            else arr.sort((a, b) => (b.criado_em || '').localeCompare(a.criado_em || ''));
            return arr;
        },

        // Aplica busca + status + filtros (f) e ordena. Reutilizado pela lista e pela prévia.
        computar(f, status) {
            const q = this.normalizar(this.busca);
            const lista = this.orcamentos.filter(o => {
                if (q && !(this.normalizar(o.cliente).includes(q) || this.normalizar(o.veiculo).includes(q) || this.normalizar(o.placa).includes(q))) return false;
                if (status !== 'todos' && o.status !== status) return false;
                return this.matchValidade(o, f.validade) && this.matchValor(o, f.valor)
                    && this.matchVinculo(o, f.vinculo) && this.matchPeriodo(o, f.periodo);
            });
            return this.ordenarLista(lista, f.ordenar);
        },

        get filtrados() { return this.computar(this.ativo, this.statusAtivo); },
        get previaCount() { return this.computar(this.draft, this.statusAtivo).length; },

        // Métricas reativas (refletem o que está filtrado)
        get temFiltro() { return this.busca.trim() !== '' || this.statusAtivo !== 'todos' || this.filtrosAtivosCount > 0; },
        get pendentesFiltrado() { return this.filtrados.filter(o => o.status === 'pendente').length; },
        get aprovadosFiltrado() { return this.filtrados.filter(o => o.status === 'aprovado').length; },
        get valorFiltrado() { return this.filtrados.reduce((s, o) => s + parseFloat(o.total || 0), 0); },
        formatarValorInt(v) { return parseFloat(v || 0).toLocaleString('pt-BR', { maximumFractionDigits: 0 }); },

        get filtrosAtivosCount() {
            let n = 0;
            if (this.ativo.validade !== 'todas') n++;
            if (this.ativo.valor !== 'qualquer') n++;
            if (this.ativo.vinculo !== 'todos') n++;
            if (this.ativo.periodo !== 'qualquer') n++;
            return n;
        },

        // ── Ações do painel ──
        abrirPainel() { this.draft = { ...this.ativo }; this.painelAberto = true; },
        aplicar() { this.ativo = { ...this.draft }; this.painelAberto = false; },
        limparDraft() { this.draft = padroes(); },
        limparTudo() { this.busca = ''; this.statusAtivo = 'todos'; this.ativo = padroes(); this.draft = padroes(); this.painelAberto = false; },

        // ── Estilos ──
        chipStyle(s) {
            const ativo = this.statusAtivo === s;
            const off = 'background:#fff;color:#475569;border:1px solid var(--color-border);';
            if (!ativo) return off;
            const on = {
                todos:    'background:#0f172a;color:#fff;border:1px solid #0f172a;',
                rascunho: 'background:rgba(100,116,139,0.14);color:#475569;border:1px solid rgba(100,116,139,0.45);',
                pendente: 'background:rgba(245,158,11,0.16);color:#b45309;border:1px solid rgba(245,158,11,0.5);',
                aprovado: 'background:rgba(16,185,129,0.16);color:#047857;border:1px solid rgba(16,185,129,0.5);',
            };
            return on[s] || off;
        },
        pillStyle(field, val) {
            return this.draft[field] === val
                ? 'background:rgba(59,130,246,0.1);border:1px solid #3B82F6;color:#1d4ed8;font-weight:600;'
                : 'background:#f8fafc;border:1px solid var(--color-border);color:#475569;font-weight:500;';
        },

        statusInfo(s) {
            const m = {
                aprovado: 'background:rgba(16,185,129,0.1);color:#059669;',
                pendente: 'background:rgba(245,158,11,0.1);color:#D97706;',
                rascunho: 'background:rgba(100,116,139,0.1);color:#64748b;',
            };
            return m[s] || m.rascunho;
        },

        statusLabel(s) {
            const m = { aprovado: 'Aprovado', pendente: 'Pendente', rascunho: 'Rascunho' };
            return m[s] || s;
        },

        formatarValor(v) {
            return parseFloat(v || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        },

        formatarDataCurta(d) {
            if (!d) return '—';
            const [, m, dd] = d.split('-');
            return `${dd}/${m}`;
        },

        formatarDataLonga(d) {
            if (!d) return '—';
            const [y, m, dd] = d.split('-');
            return `${dd}/${m}/${y}`;
        },
    };
}
</script>

</x-layouts.oficina>
