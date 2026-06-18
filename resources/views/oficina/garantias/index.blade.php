<x-layouts.oficina title="Garantias">

    <script>
        window.__garantias = {!! json_encode($garantias, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__metricas  = {!! json_encode($metricas,  JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    </script>

    <div x-data="garantiasPage()" x-init="init()" @keydown.escape.window="fecharModais()">

        {{-- ==================== HEADER ==================== --}}
        <div class="flex items-center gap-3 mb-6">
            <h2 class="font-display font-bold text-void text-xl">Garantias</h2>
            <span class="font-mono text-xs px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 font-semibold"
                  x-text="metricas.ativas + ' ativas'"></span>
            <span x-show="metricas.vencendo > 0"
                  class="font-mono text-xs px-2 py-0.5 rounded-full font-semibold"
                  style="background:rgba(245,158,11,0.12); color:#D97706;"
                  x-text="metricas.vencendo + ' vencendo'"></span>
        </div>

        {{-- ==================== BLOCO 1 — VENCENDO EM BREVE ==================== --}}
        <div x-show="garantiasVencendo.length > 0" class="mb-6">
            <div class="rounded-xl overflow-hidden" style="border: 1.5px solid #F59E0B;">
                <div class="flex items-center gap-2 px-5 py-3 bg-amber-50">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <h3 class="font-display font-semibold text-amber-700 text-sm">Vencendo em breve</h3>
                    <span class="font-mono text-xs px-1.5 py-0.5 rounded-full font-bold"
                          style="background:rgba(245,158,11,0.15); color:#D97706;"
                          x-text="garantiasVencendo.length"></span>
                </div>
                <div class="bg-white px-5 py-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <template x-for="g in garantiasVencendo" :key="g.id">
                            <div class="p-4 rounded-lg bg-amber-50/50" style="border: 1px solid rgba(245,158,11,0.2);">
                                <p class="font-semibold text-void text-sm mb-0.5" x-text="g.cliente"></p>
                                <p class="text-xs text-muted mb-1 truncate" x-text="g.veiculo.split('·')[0].trim()"></p>
                                <a :href="'/oficina/os/' + g.os_id"
                                   class="font-mono text-xs text-ocean hover:underline font-medium" x-text="g.os_id"></a>
                                <div class="flex items-center justify-between mt-3">
                                    <span class="text-xs font-bold text-amber-600"
                                          x-text="'Vence em ' + g.dias_restantes + (g.dias_restantes === 1 ? ' dia' : ' dias')"></span>
                                    <button type="button"
                                            @click="abrirModalAcionar(g.id)"
                                            class="text-xs font-semibold text-spark hover:underline">
                                        Acionar
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== BLOCO 3 — TODAS AS GARANTIAS ==================== --}}
        <div>
            <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                <h3 class="font-display font-semibold text-void text-base">Todas as garantias</h3>
                <button type="button" @click="abrirModalRegistrar()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-spark text-white text-sm font-medium hover:bg-spark/90 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Registrar manualmente
                </button>
            </div>

            {{-- Filtros + busca --}}
            <div class="flex flex-col sm:flex-row gap-3 mb-5">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none"
                         fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    <input type="text" x-model="busca"
                           placeholder="Buscar por cliente, veículo ou OS..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-border bg-white text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                </div>
                <div class="flex gap-1.5 flex-wrap">
                    <template x-for="f in filtros" :key="f.valor">
                        <button type="button" @click="filtro = f.valor"
                                class="px-3 py-2 rounded-lg text-xs font-medium border transition-all"
                                :class="filtro === f.valor
                                    ? 'bg-void text-white border-void'
                                    : 'bg-white text-muted border-border hover:border-void/30 hover:text-void'">
                            <span x-text="f.label"></span>
                            <span class="ml-1 font-mono" x-text="'(' + f.count + ')'"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Empty state --}}
            <template x-if="garantiasFiltradas.length === 0">
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-14 h-14 rounded-full bg-surface flex items-center justify-center mb-4"
                         style="border: 1px solid var(--color-border);">
                        <svg class="w-7 h-7 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <p class="font-display font-semibold text-void text-base">Nenhuma garantia encontrada.</p>
                </div>
            </template>

            {{-- Cards --}}
            <div class="space-y-4">
                <template x-for="g in garantiasFiltradas" :key="g.id">
                    <div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid var(--color-border);">

                        {{-- Cabeçalho --}}
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3 flex-wrap mb-3">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold text-white"
                                          :style="'background:' + statusCor(g.status)"
                                          x-text="statusLabel(g.status)"></span>
                                    <span class="font-mono text-xs font-semibold text-void" x-text="g.id"></span>
                                    <a :href="'/oficina/os/' + g.os_id"
                                       class="font-mono text-xs text-ocean hover:underline font-medium" x-text="g.os_id"></a>
                                </div>

                                {{-- Dias restantes --}}
                                <div class="text-right">
                                    <template x-if="g.status === 'ativa' || g.status === 'vencendo'">
                                        <p class="text-xs font-semibold"
                                           :class="g.status === 'vencendo' ? 'text-amber-600' : 'text-muted'"
                                           x-text="g.dias_restantes + (g.dias_restantes === 1 ? ' dia restante' : ' dias restantes')"></p>
                                    </template>
                                    <template x-if="g.status === 'expirada'">
                                        <p class="text-xs font-semibold text-red-500"
                                           x-text="'Expirada há ' + Math.abs(g.dias_restantes) + ' dias'"></p>
                                    </template>
                                    <template x-if="g.status === 'acionada'">
                                        <a :href="'#'"
                                           class="text-xs font-semibold text-violet-600 hover:underline"
                                           x-text="'Retrabalho: ' + g.os_retrabalho_id"></a>
                                    </template>
                                </div>
                            </div>

                            <p class="font-semibold text-void text-sm mb-0.5" x-text="g.cliente"></p>
                            <p class="text-xs text-muted mb-3" x-text="g.veiculo"></p>

                            <div class="flex items-center gap-4 text-xs text-muted flex-wrap">
                                <span>Entrega: <span class="font-medium text-void" x-text="formatarData(g.data_entrega)"></span></span>
                                <span>·</span>
                                <span>Vencimento: <span class="font-medium"
                                      :class="g.status === 'vencendo' ? 'text-amber-600' : g.status === 'expirada' ? 'text-red-500' : 'text-void'"
                                      x-text="formatarData(g.data_vencimento)"></span></span>
                            </div>
                        </div>

                        {{-- Botão acionar --}}
                        <div class="flex items-center justify-end border-t px-5 py-3" style="border-color: var(--color-border);">
                            <button type="button"
                                    @click="abrirModalAcionar(g.id)"
                                    :disabled="g.status === 'acionada' || g.status === 'expirada'"
                                    :title="g.status === 'acionada' ? 'Garantia já acionada' : g.status === 'expirada' ? 'Garantia expirada' : 'Acionar garantia'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                                    :class="g.status === 'acionada' || g.status === 'expirada'
                                        ? 'bg-surface text-muted cursor-not-allowed border border-border'
                                        : 'bg-spark/10 text-spark hover:bg-spark/20 border border-spark/20 hover:border-spark/40'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span x-text="g.status === 'acionada' ? 'Acionada' : 'Acionar'"></span>
                            </button>
                        </div>

                    </div>
                </template>
            </div>
        </div>

        {{-- ==================== TOAST ==================== --}}
        <div x-show="toastVisible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg bg-void text-white text-sm font-medium"
             style="pointer-events:none;">
            <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span x-text="toastMsg"></span>
        </div>

        {{-- ==================== MODAL ACIONAR GARANTIA ==================== --}}
        <template x-teleport="body">
            <div x-show="maAberto"
                 class="fixed inset-0 z-40 flex items-center justify-center p-4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-void/50" @click="fecharModais()"></div>
                <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     @click.stop>

                    <div class="flex items-center justify-between px-6 py-4" style="border-bottom: 1px solid var(--color-border);">
                        <div>
                            <h3 class="font-display font-semibold text-void text-base">Acionar garantia</h3>
                            <p class="text-xs text-muted mt-0.5" x-text="'Abrindo OS de retrabalho vinculada a ' + maGarantiaId"></p>
                        </div>
                        <button type="button" @click="fecharModais()"
                                class="text-muted hover:text-void transition-colors p-1 rounded-lg hover:bg-surface">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 space-y-4">
                        {{-- Info da garantia --}}
                        <div x-show="maGarantia" class="p-3 rounded-lg bg-surface" style="border: 1px solid var(--color-border);">
                            <p class="text-xs font-semibold text-void" x-text="maGarantia?.cliente"></p>
                            <p class="text-xs text-muted mt-0.5" x-text="maGarantia?.veiculo"></p>
                            <p class="text-xs text-muted mt-0.5">
                                OS origem: <a :href="'/oficina/os/' + maGarantia?.os_id"
                                              class="font-mono font-medium text-ocean hover:underline"
                                              x-text="maGarantia?.os_id"></a>
                            </p>
                            <p class="text-xs text-muted mt-0.5">
                                Garantia válida até: <span class="font-medium text-void" x-text="formatarData(maGarantia?.data_vencimento)"></span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Descrição do problema <span class="text-spark">*</span></label>
                            <textarea x-model="maDescricao" rows="3"
                                      placeholder="Descreva o problema relatado pelo cliente..."
                                      class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Data de abertura</label>
                            <input type="date" x-model="maData"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>
                    </div>

                    <div class="flex gap-3 px-6 py-4" style="border-top: 1px solid var(--color-border);">
                        <button type="button" @click="fecharModais()"
                                class="flex-1 px-4 py-2 rounded-lg border border-border text-void text-sm font-medium hover:bg-surface transition-colors">
                            Cancelar
                        </button>
                        <button type="button" @click="acionarGarantia()"
                                :disabled="!maDescricao.trim()"
                                :class="maDescricao.trim() ? 'bg-spark text-white hover:bg-spark/90' : 'bg-border text-muted cursor-not-allowed'"
                                class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Abrir OS de retrabalho
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ==================== MODAL REGISTRAR MANUALMENTE ==================== --}}
        <template x-teleport="body">
            <div x-show="mrAberto"
                 class="fixed inset-0 z-40 flex items-center justify-center p-4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-void/50" @click="fecharModais()"></div>
                <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     @click.stop>

                    <div class="flex items-center justify-between px-6 py-4" style="border-bottom: 1px solid var(--color-border);">
                        <h3 class="font-display font-semibold text-void text-base">Registrar garantia manualmente</h3>
                        <button type="button" @click="fecharModais()"
                                class="text-muted hover:text-void transition-colors p-1 rounded-lg hover:bg-surface">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Cliente <span class="text-spark">*</span></label>
                            <input type="text" x-model="mr.cliente" placeholder="Nome do cliente..."
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Veículo <span class="text-spark">*</span></label>
                            <input type="text" x-model="mr.veiculo" placeholder="Ex: Honda Civic 2019 · ABC-1234"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Referência OS <span class="text-muted font-normal">(opcional)</span></label>
                            <input type="text" x-model="mr.os_ref" placeholder="Ex: OS-2025-0047"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-muted mb-1.5">Data de entrega <span class="text-spark">*</span></label>
                                <input type="date" x-model="mr.data_entrega"
                                       class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-muted mb-1.5">Prazo (dias)</label>
                                <input type="number" min="1" max="365" x-model="mr.prazo"
                                       class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                            </div>
                        </div>
                        <div x-show="mr.data_entrega" class="text-xs text-muted">
                            Vencimento calculado: <span class="font-semibold text-void" x-text="calcularVencimentoPreview()"></span>
                        </div>
                    </div>

                    <div class="flex gap-3 px-6 py-4" style="border-top: 1px solid var(--color-border);">
                        <button type="button" @click="fecharModais()"
                                class="flex-1 px-4 py-2 rounded-lg border border-border text-void text-sm font-medium hover:bg-surface transition-colors">
                            Cancelar
                        </button>
                        <button type="button" @click="registrarManualmente()"
                                :disabled="!mrValido"
                                :class="mrValido ? 'bg-spark text-white hover:bg-spark/90' : 'bg-border text-muted cursor-not-allowed'"
                                class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Registrar
                        </button>
                    </div>
                </div>
            </div>
        </template>

    </div>

    <script>
        function garantiasPage() {
            return {
                garantias: [],
                metricas:  {},
                hoje:               '',
                filtro:             'todas',
                busca:              '',

                // Modal acionar
                maAberto:     false,
                maGarantiaId: null,
                maDescricao:  '',
                maData:       '',

                // Modal registrar
                mrAberto: false,
                mr: { cliente: '', veiculo: '', os_ref: '', data_entrega: '', prazo: 90 },

                toastVisible: false,
                toastMsg:     '',
                _toastTimer:  null,

                statusCfg: {
                    ativa:    { cor: '#10B981', label: 'Ativa' },
                    vencendo: { cor: '#F59E0B', label: 'Vencendo' },
                    expirada: { cor: '#EF4444', label: 'Expirada' },
                    acionada: { cor: '#7C3AED', label: 'Acionada' },
                },

                init() {
                    this.garantias = window.__garantias || [];
                    this.metricas  = window.__metricas  || {};
                    this.hoje      = new Date().toISOString().split('T')[0];
                    this.maData    = this.hoje;
                },

                // ── Computed ─────────────────────────────────────────────────

                get garantiasVencendo() {
                    return this.garantias.filter(g => g.status === 'vencendo');
                },

                get filtros() {
                    const conta = s => this.garantias.filter(g =>
                        s === 'todas' ? true : g.status === s
                    ).length;
                    return [
                        { valor: 'todas',    label: 'Todas',    count: conta('todas') },
                        { valor: 'ativa',    label: 'Ativas',   count: conta('ativa') },
                        { valor: 'vencendo', label: 'Vencendo', count: conta('vencendo') },
                        { valor: 'expirada', label: 'Expiradas',count: conta('expirada') },
                        { valor: 'acionada', label: 'Acionadas',count: conta('acionada') },
                    ];
                },

                get garantiasFiltradas() {
                    const order = { vencendo: 0, ativa: 1, acionada: 2, expirada: 3 };
                    let r = [...this.garantias];

                    if (this.filtro !== 'todas') {
                        r = r.filter(g => g.status === this.filtro);
                    }

                    if (this.busca.trim()) {
                        const q = this.busca.toLowerCase();
                        r = r.filter(g =>
                            g.cliente.toLowerCase().includes(q) ||
                            g.veiculo.toLowerCase().includes(q) ||
                            g.os_id.toLowerCase().includes(q) ||
                            g.id.toLowerCase().includes(q)
                        );
                    }

                    return r.sort((a, b) => {
                        const d = (order[a.status] ?? 5) - (order[b.status] ?? 5);
                        if (d !== 0) return d;
                        return a.data_vencimento.localeCompare(b.data_vencimento);
                    });
                },

                get maGarantia() {
                    return this.garantias.find(g => g.id === this.maGarantiaId) || null;
                },

                get mrValido() {
                    return this.mr.cliente.trim() !== '' &&
                           this.mr.veiculo.trim() !== '' &&
                           this.mr.data_entrega !== '';
                },

                // ── Helpers ──────────────────────────────────────────────────

                statusCor(s)   { return this.statusCfg[s]?.cor   || '#94A3B8'; },
                statusLabel(s) { return this.statusCfg[s]?.label || s; },

                formatarData(d) {
                    if (!d) return '—';
                    const [y, m, dd] = d.split('-');
                    return `${dd}/${m}/${y}`;
                },

                calcularVencimentoPreview() {
                    if (!this.mr.data_entrega) return '—';
                    const d = new Date(this.mr.data_entrega + 'T12:00:00');
                    d.setDate(d.getDate() + parseInt(this.mr.prazo || 90));
                    return this.formatarData(d.toISOString().split('T')[0]);
                },

                adicionarDias(dateStr, dias) {
                    const d = new Date(dateStr + 'T12:00:00');
                    d.setDate(d.getDate() + dias);
                    return d.toISOString().split('T')[0];
                },

                recalcularStatus(dataVencimento) {
                    const diff = Math.floor((new Date(dataVencimento) - new Date(this.hoje)) / 86400000);
                    if (diff < 0)   return 'expirada';
                    if (diff <= 10) return 'vencendo';
                    return 'ativa';
                },

                // ── Ações — acionar garantia ──────────────────────────────────

                abrirModalAcionar(garantiaId) {
                    this.maGarantiaId = garantiaId;
                    this.maDescricao  = '';
                    this.maData       = this.hoje;
                    this.maAberto     = true;
                },

                acionarGarantia() {
                    const g = this.garantias.find(g => g.id === this.maGarantiaId);
                    if (!g) return;

                    g.os_retrabalho_id = 'OS-RET-' + Date.now();
                    g.status           = 'acionada';

                    // Atualiza métricas
                    this.metricas.ativas   = this.garantias.filter(g => g.status === 'ativa').length;
                    this.metricas.vencendo = this.garantias.filter(g => g.status === 'vencendo').length;
                    this.metricas.acionadas= this.garantias.filter(g => g.status === 'acionada').length;

                    this.maAberto = false;
                    this.mostrarToast('Garantia acionada — OS de retrabalho criada!');
                },

                // ── Ações — registrar manualmente ────────────────────────────

                abrirModalRegistrar() {
                    this.mr = { cliente: '', veiculo: '', os_ref: '', data_entrega: '', prazo: 90 };
                    this.mrAberto = true;
                },

                registrarManualmente() {
                    if (!this.mrValido) return;

                    const prazo          = parseInt(this.mr.prazo) || 90;
                    const dataVencimento = this.adicionarDias(this.mr.data_entrega, prazo);
                    const status         = this.recalcularStatus(dataVencimento);
                    const id             = 'GARAN-MANUAL-' + Date.now();

                    const novaGarantia = {
                        id,
                        tenant_id:        1,
                        os_id:            this.mr.os_ref || '—',
                        cliente_id:       null,
                        cliente:          this.mr.cliente.trim(),
                        veiculo_id:       null,
                        veiculo:          this.mr.veiculo.trim(),
                        data_entrega:     this.mr.data_entrega,
                        data_vencimento:  dataVencimento,
                        status,
                        os_retrabalho_id: null,
                        dias_restantes:   Math.floor((new Date(dataVencimento) - new Date(this.hoje)) / 86400000),
                    };

                    this.garantias.push(novaGarantia);

                    if (status === 'ativa')    this.metricas.ativas++;
                    if (status === 'vencendo') this.metricas.vencendo++;

                    this.mrAberto = false;
                    this.mostrarToast('Garantia registrada com sucesso!');
                },

                // ── Utilitários ──────────────────────────────────────────────

                fecharModais() {
                    this.maAberto = false;
                    this.mrAberto = false;
                },

                mostrarToast(msg) {
                    this.toastMsg     = msg;
                    this.toastVisible = true;
                    clearTimeout(this._toastTimer);
                    this._toastTimer = setTimeout(() => { this.toastVisible = false; }, 3000);
                },
            };
        }
    </script>

</x-layouts.oficina>
