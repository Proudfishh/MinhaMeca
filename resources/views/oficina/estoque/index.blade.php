<x-layouts.oficina title="Estoque">

    <script>
        window.__itens    = {!! json_encode($itens,    JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__metricas = {!! json_encode($metricas, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    </script>

    <div x-data="estoquePage()" x-init="init()">

        {{-- ==================== HEADER ==================== --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <h2 class="font-display font-bold text-void text-xl">Estoque</h2>
                <span class="font-mono text-xs px-2 py-0.5 rounded-full bg-ocean/10 text-ocean font-semibold"
                      x-text="metricas.total + ' itens'"></span>
                <span x-show="metricas.baixo > 0 || metricas.sem_estoque > 0"
                      class="font-mono text-xs px-2 py-0.5 rounded-full font-semibold"
                      style="background: rgba(245,158,11,0.12); color: #D97706;"
                      x-text="(metricas.baixo + metricas.sem_estoque) + ' alerta(s)'"></span>
            </div>
            <button type="button"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-spark text-white text-sm font-medium hover:bg-spark/90 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Item
            </button>
        </div>

        {{-- ==================== MÉTRICAS ==================== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
                <p class="text-muted text-xs font-medium uppercase tracking-wide mb-1">Total de Itens</p>
                <p class="font-display font-bold text-void text-2xl" x-text="metricas.total"></p>
            </div>
            <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
                <p class="text-muted text-xs font-medium uppercase tracking-wide mb-1">Estoque Baixo</p>
                <p class="font-display font-bold text-2xl"
                   :class="metricas.baixo > 0 ? 'text-amber-600' : 'text-void'"
                   x-text="metricas.baixo"></p>
            </div>
            <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
                <p class="text-muted text-xs font-medium uppercase tracking-wide mb-1">Sem Estoque</p>
                <p class="font-display font-bold text-2xl"
                   :class="metricas.sem_estoque > 0 ? 'text-red-600' : 'text-void'"
                   x-text="metricas.sem_estoque"></p>
            </div>
            <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
                <p class="text-muted text-xs font-medium uppercase tracking-wide mb-1">Valor em Estoque</p>
                <p class="font-display font-bold text-void text-2xl"
                   x-text="'R$ ' + metricas.valor_total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })"></p>
            </div>
        </div>

        {{-- ==================== BUSCA + FILTRO ==================== --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-5">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none"
                     fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                <input type="text" x-model="busca"
                       placeholder="Buscar item..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-border bg-white text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
            </div>
            <div class="flex gap-1.5">
                <template x-for="f in ['todos', 'baixo', 'sem_estoque']" :key="f">
                    <button type="button" @click="filtro = f"
                            class="px-3 py-2 rounded-lg text-xs font-medium border transition-all"
                            :class="filtro === f
                                ? 'bg-void text-white border-void'
                                : 'bg-white text-muted border-border hover:border-void/30 hover:text-void'"
                            x-text="f === 'todos' ? 'Todos' : f === 'baixo' ? 'Baixo' : 'Sem estoque'">
                    </button>
                </template>
            </div>
        </div>

        {{-- ==================== TABELA ==================== --}}
        <div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid var(--color-border);">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--color-border);">
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Descrição</th>
                        <th class="text-center px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Qtd</th>
                        <th class="text-center px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Mínimo</th>
                        <th class="text-right px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Valor Unit.</th>
                        <th class="text-center px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="itensFiltrados.length === 0">
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-muted text-sm">
                                Nenhum item encontrado.
                            </td>
                        </tr>
                    </template>
                    <template x-for="item in itensFiltrados" :key="item.id">
                        <tr class="hover:bg-surface transition-colors" style="border-bottom: 1px solid var(--color-border);">
                            <td class="px-5 py-3.5">
                                <span class="text-void font-medium text-sm" x-text="item.descricao"></span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="font-mono font-bold text-sm"
                                      :class="item.quantidade === 0 ? 'text-red-600' : item.status === 'baixo' ? 'text-amber-600' : 'text-void'"
                                      x-text="item.quantidade"></span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="font-mono text-xs text-muted" x-text="item.estoque_minimo"></span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="font-mono text-sm text-void"
                                      x-text="'R$ ' + item.valor_unitario.toLocaleString('pt-BR', { minimumFractionDigits: 2 })"></span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <template x-if="item.status === 'ok'">
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium"
                                          style="background: rgba(16,185,129,0.1); color: #10B981;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        OK
                                    </span>
                                </template>
                                <template x-if="item.status === 'baixo'">
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium"
                                          style="background: rgba(245,158,11,0.1); color: #D97706;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Baixo
                                    </span>
                                </template>
                                <template x-if="item.status === 'sem_estoque'">
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium"
                                          style="background: rgba(239,68,68,0.1); color: #DC2626;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Zerado
                                    </span>
                                </template>
                            </td>
                            <td class="px-5 py-3.5">
                                <button type="button"
                                        class="text-spark text-xs font-medium hover:underline">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        function estoquePage() {
            return {
                itens:    [],
                metricas: {},
                busca:    '',
                filtro:   'todos',

                init() {
                    this.itens    = window.__itens    || [];
                    this.metricas = window.__metricas || {};
                },

                get itensFiltrados() {
                    let lista = this.itens;
                    if (this.filtro !== 'todos') {
                        lista = lista.filter(i => i.status === this.filtro);
                    }
                    if (this.busca.trim()) {
                        const q = this.busca.toLowerCase();
                        lista = lista.filter(i => i.descricao.toLowerCase().includes(q));
                    }
                    return lista;
                },
            };
        }
    </script>

</x-layouts.oficina>
