<x-layouts.oficina title="Veículos">

    <script>
        window.__veiculos = {!! json_encode($veiculos, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__etapas   = {!! json_encode($etapas,   JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    </script>

    <div x-data="veiculosPage()" x-init="init()">

        {{-- ==================== HEADER ==================== --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <h2 class="font-display font-bold text-void text-xl">Veículos</h2>
                <span class="font-mono text-xs px-2 py-0.5 rounded-full bg-ocean/10 text-ocean font-semibold"
                      x-text="veiculos.length"></span>
            </div>
        </div>

        {{-- ==================== BUSCA ==================== --}}
        <div class="relative mb-6">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none"
                 fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
            <input type="text" x-model="busca"
                   placeholder="Buscar por placa, modelo ou cliente..."
                   class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-border bg-white text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
        </div>

        {{-- ==================== LISTA ==================== --}}
        <template x-if="veiculosFiltrados.length === 0">
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-14 h-14 rounded-full bg-surface flex items-center justify-center mb-4"
                     style="border: 1px solid var(--color-border);">
                    <svg class="w-7 h-7 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z"/>
                    </svg>
                </div>
                <p class="font-display font-semibold text-void text-base mb-1">Nenhum veículo encontrado.</p>
            </div>
        </template>

        {{-- MOBILE: cards --}}
        <div class="md:hidden space-y-2">
            <template x-if="veiculosFiltrados.length > 0">
                <div class="rounded-2xl overflow-hidden bg-white"
                     style="border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <template x-for="(v, i) in veiculosFiltrados" :key="v.id">
                        <a :href="'/oficina/veiculos/' + v.id"
                           class="flex items-center gap-3 px-4 py-4 transition-all duration-200 active:bg-[#F8FAFC]"
                           :style="i < veiculosFiltrados.length - 1 ? 'border-bottom: 1px solid rgba(0,0,0,0.05)' : ''">

                            {{-- ícone do veículo --}}
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background: rgba(30,58,95,0.07); border: 1px solid rgba(30,58,95,0.12);">
                                <svg style="width:18px;height:18px;" fill="none" stroke="#1E3A5F" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z"/>
                                </svg>
                            </div>

                            {{-- info principal --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="font-semibold text-void text-sm leading-snug truncate" x-text="v.marca + ' ' + v.modelo"></p>
                                    <span class="font-mono text-[11px] px-1.5 py-0.5 rounded flex-shrink-0"
                                          style="background: rgba(0,0,0,0.05); color: var(--color-muted);"
                                          x-text="v.placa"></span>
                                </div>
                                <p class="text-muted text-xs truncate" x-text="v.ano + ' · ' + v.cor + ' · ' + v.cliente"></p>
                                <div class="mt-1.5 flex items-center gap-2">
                                    <template x-if="v.os_ativa">
                                        <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-full font-medium"
                                              :style="'background:' + etapaInfo(v.os_ativa.etapa_atual).cor + '15; color:' + etapaInfo(v.os_ativa.etapa_atual).cor">
                                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                                  :style="'background:' + etapaInfo(v.os_ativa.etapa_atual).cor"></span>
                                            <span x-text="etapaInfo(v.os_ativa.etapa_atual).label"></span>
                                        </span>
                                    </template>
                                    <span class="text-[11px] text-muted" x-text="v.total_os + ' OS'"></span>
                                </div>
                            </div>

                            {{-- chevron --}}
                            <svg class="flex-shrink-0" style="width:18px;height:18px;color:#CBD5E1;"
                                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                            </svg>
                        </a>
                    </template>
                </div>
            </template>
        </div>

        {{-- DESKTOP: tabela --}}
        <div class="hidden md:block bg-white rounded-xl overflow-hidden" style="border: 1px solid var(--color-border);">
            <template x-if="veiculosFiltrados.length > 0">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--color-border);">
                            <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Veículo</th>
                            <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Placa</th>
                            <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Cliente</th>
                            <th class="text-center px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Total OS</th>
                            <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">OS Ativa</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="v in veiculosFiltrados" :key="v.id">
                            <tr class="hover:bg-surface transition-colors" style="border-bottom: 1px solid var(--color-border);">
                                <td class="px-5 py-3.5">
                                    <div>
                                        <p class="font-medium text-void text-sm" x-text="v.marca + ' ' + v.modelo"></p>
                                        <p class="text-xs text-muted" x-text="v.ano + ' · ' + v.cor"></p>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-mono text-sm text-void" x-text="v.placa"></span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-void text-sm" x-text="v.cliente"></span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="font-mono text-sm text-void" x-text="v.total_os"></span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <template x-if="v.os_ativa">
                                        <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-md font-medium"
                                              :style="'background:' + etapaInfo(v.os_ativa.etapa_atual).cor + '18; color:' + etapaInfo(v.os_ativa.etapa_atual).cor">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                  :style="'background:' + etapaInfo(v.os_ativa.etapa_atual).cor"></span>
                                            <span x-text="v.os_ativa.id"></span>
                                        </span>
                                    </template>
                                    <template x-if="!v.os_ativa">
                                        <span class="text-xs text-muted">—</span>
                                    </template>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a :href="'/oficina/veiculos/' + v.id"
                                       class="text-spark text-xs font-medium hover:underline">Ver →</a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
        </div>

    </div>

    <script>
        function veiculosPage() {
            return {
                veiculos: [],
                etapas:   {},
                busca:    '',

                init() {
                    this.veiculos = window.__veiculos || [];
                    this.etapas   = window.__etapas   || {};
                },

                get veiculosFiltrados() {
                    if (!this.busca.trim()) return this.veiculos;
                    const q = this.busca.toLowerCase();
                    return this.veiculos.filter(v =>
                        v.placa.toLowerCase().includes(q) ||
                        (v.marca + ' ' + v.modelo).toLowerCase().includes(q) ||
                        v.cliente.toLowerCase().includes(q)
                    );
                },

                etapaInfo(key) {
                    return this.etapas[key] || { label: key, cor: '#94A3B8' };
                },
            };
        }
    </script>

</x-layouts.oficina>
