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
              style="background:rgba(59,130,246,0.1);color:#3B82F6;">
            {{ $metricas['total'] }} total
        </span>
        @if($metricas['pendente'] > 0)
        <span class="font-mono text-xs px-2 py-0.5 rounded-full font-semibold"
              style="background:rgba(245,158,11,0.12);color:#D97706;">
            {{ $metricas['pendente'] }} pendente(s)
        </span>
        @endif
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
        <p class="font-mono font-bold text-void text-lg">{{ $metricas['total'] }}</p>
    </div>
    <div class="bg-white rounded-xl px-4 py-3" style="border:1px solid var(--color-border);box-shadow:0 1px 4px rgba(0,0,0,0.04);">
        <p class="text-muted text-[10px] font-semibold uppercase tracking-wide mb-1">Pendentes</p>
        <p class="font-mono font-bold text-lg {{ $metricas['pendente'] > 0 ? 'text-amber-600' : 'text-void' }}">{{ $metricas['pendente'] }}</p>
    </div>
    <div class="bg-white rounded-xl px-4 py-3" style="border:1px solid var(--color-border);box-shadow:0 1px 4px rgba(0,0,0,0.04);">
        <p class="text-muted text-[10px] font-semibold uppercase tracking-wide mb-1">Aprovados</p>
        <p class="font-mono font-bold text-emerald-600 text-lg">{{ $metricas['aprovado'] }}</p>
    </div>
    <div class="bg-white rounded-xl px-4 py-3" style="border:1px solid var(--color-border);box-shadow:0 1px 4px rgba(0,0,0,0.04);">
        <p class="text-muted text-[10px] font-semibold uppercase tracking-wide mb-1">Valor total</p>
        <p class="font-mono font-bold text-void text-lg">R$ {{ number_format($metricas['valor'], 0, ',', '.') }}</p>
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

        {{-- Resultado vazio da busca --}}
        <template x-if="filtrados.length === 0 && busca.trim().length > 0">
            <div class="flex flex-col items-center justify-center py-14 text-center">
                <p class="text-void text-sm font-semibold mb-1">Nenhum resultado para "<span x-text="busca"></span>"</p>
                <p class="text-muted text-xs">Tente buscar por placa, nome do cliente ou veículo</p>
            </div>
        </template>

        {{-- MOBILE: cards --}}
        <div class="md:hidden rounded-2xl overflow-hidden mb-20" style="border:1px solid rgba(0,0,0,0.06);box-shadow:0 2px 8px rgba(0,0,0,0.04);">
            <template x-for="(orc, i) in filtrados" :key="orc.id">
                <a :href="'{{ url('oficina/orcamentos') }}/' + orc.id"
                   class="flex items-center gap-3 px-4 py-4 bg-white active:bg-slate-50 transition-colors"
                   :style="i < filtrados.length - 1 ? 'border-bottom:1px solid rgba(0,0,0,0.05);' : ''">
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
            </template>
        </div>

        {{-- DESKTOP: tabela --}}
        <div class="hidden md:block bg-white rounded-xl overflow-hidden" style="border:1px solid var(--color-border);">
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
    return {
        busca: '',
        orcamentos: [],

        init() {
            this.orcamentos = window.__orcamentos || [];
        },

        normalizar(s) {
            return (s || '').toLowerCase().normalize('NFD').replace(/\p{M}/gu, '');
        },

        get filtrados() {
            const q = this.normalizar(this.busca);
            if (!q) return this.orcamentos;
            return this.orcamentos.filter(o =>
                this.normalizar(o.cliente).includes(q) ||
                this.normalizar(o.veiculo).includes(q) ||
                this.normalizar(o.placa).includes(q)
            );
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
