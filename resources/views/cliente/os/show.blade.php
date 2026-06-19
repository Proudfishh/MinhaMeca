<x-layouts.cliente :title="$os['id']">

    <script>
        window.__os     = {!! json_encode($os,     JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__etapas = {!! json_encode($etapas, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    </script>

    <div x-data="osDetalhe()" x-init="init()">

        {{-- Voltar --}}
        <a href="{{ route('cliente.veiculos.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-muted hover:text-void transition-colors mb-5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar ao portal
        </a>

        {{-- Cabeçalho --}}
        <div class="bg-white rounded-xl p-5 mb-4" style="border: 1px solid var(--color-border);">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                     :style="'background:' + etapaInfo(os.etapa_atual).cor + '15; border: 1.5px solid ' + etapaInfo(os.etapa_atual).cor + '30'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"
                         :style="'color:' + etapaInfo(os.etapa_atual).cor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="font-mono text-xs text-muted" x-text="os.id"></span>
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium"
                              :style="'background:' + etapaInfo(os.etapa_atual).cor + '18; color:' + etapaInfo(os.etapa_atual).cor">
                            <span class="w-1.5 h-1.5 rounded-full"
                                  :style="'background:' + etapaInfo(os.etapa_atual).cor"></span>
                            <span x-text="etapaInfo(os.etapa_atual).label"></span>
                        </span>
                    </div>
                    <h2 class="font-display font-bold text-void text-lg leading-tight" x-text="os.veiculo"></h2>
                    <div class="flex flex-wrap gap-4 mt-3 text-xs text-muted">
                        <span>Entrada: <strong class="text-void font-mono" x-text="formatarData(os.data_entrada)"></strong></span>
                        <template x-if="os.previsao_entrega">
                            <span>Previsão: <strong class="text-void font-mono" x-text="formatarData(os.previsao_entrega)"></strong></span>
                        </template>
                        <template x-if="os.mecanico">
                            <span>Mecânico: <strong class="text-void" x-text="os.mecanico"></strong></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progresso --}}
        <div class="bg-white rounded-xl p-5 mb-4" style="border: 1px solid var(--color-border);">
            <p class="text-[10px] font-semibold text-muted uppercase tracking-wide mb-4">Andamento</p>
            <div class="flex items-center">
                <template x-for="(etapa, key, idx) in etapas" :key="key">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="flex flex-col items-center flex-shrink-0">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center transition-all"
                                 :style="etapaIndex(os.etapa_atual) > etapaKeys.indexOf(key)
                                     ? 'background:' + etapa.cor + '30'
                                     : os.etapa_atual === key
                                         ? 'background:' + etapa.cor
                                         : 'background: var(--color-border)'">
                                <template x-if="etapaIndex(os.etapa_atual) > etapaKeys.indexOf(key)">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"
                                         :style="'color:' + etapa.cor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </template>
                                <template x-if="os.etapa_atual === key">
                                    <div class="w-2.5 h-2.5 rounded-full bg-white"></div>
                                </template>
                            </div>
                            <span class="text-[9px] mt-1 text-center leading-tight w-14 truncate"
                                  :class="os.etapa_atual === key ? 'font-bold' : 'text-muted'"
                                  :style="os.etapa_atual === key ? 'color:' + etapa.cor : ''"
                                  x-text="etapa.label"></span>
                        </div>
                        <template x-if="etapaKeys.indexOf(key) < etapaKeys.length - 1">
                            <div class="flex-1 h-0.5 mx-1 mb-4 transition-all"
                                 :style="etapaIndex(os.etapa_atual) > etapaKeys.indexOf(key)
                                     ? 'background:' + etapa.cor + '40'
                                     : 'background: var(--color-border)'">
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- Serviços --}}
        <template x-if="os.servicos && os.servicos.length > 0">
            <div class="bg-white rounded-xl p-5 mb-4" style="border: 1px solid var(--color-border);">
                <p class="text-[10px] font-semibold text-muted uppercase tracking-wide mb-4">Serviços</p>
                <div class="space-y-3">
                    <template x-for="(srv, i) in os.servicos" :key="i">
                        <div class="flex items-center gap-3">
                            <template x-if="srv.status === 'concluido'">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <template x-if="srv.status === 'em_andamento'">
                                <svg class="w-4 h-4 text-spark flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <template x-if="srv.status === 'pendente'">
                                <div class="w-4 h-4 rounded-full border-2 border-border flex-shrink-0"></div>
                            </template>
                            <span class="text-sm text-void" x-text="srv.descricao"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- Descrição --}}
        <template x-if="os.descricao_cliente">
            <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
                <p class="text-[10px] font-semibold text-muted uppercase tracking-wide mb-2">Problema relatado</p>
                <p class="text-sm text-void" x-text="os.descricao_cliente"></p>
            </div>
        </template>

    </div>

    <script>
        function osDetalhe() {
            return {
                os:       {},
                etapas:   {},
                etapaKeys: [],

                etapasCfg: {
                    checkin:     { label: 'Check-in',     cor: '#94A3B8' },
                    diagnostico: { label: 'Diagnóstico',  cor: '#3B82F6' },
                    pecas:       { label: 'Peças',        cor: '#F59E0B' },
                    servico:     { label: 'Serviço',      cor: '#7C3AED' },
                    testes:      { label: 'Testes',       cor: '#06B6D4' },
                    finalizacao: { label: 'Finalização',  cor: '#10B981' },
                },

                init() {
                    this.os        = window.__os     || {};
                    this.etapas    = window.__etapas || {};
                    this.etapaKeys = Object.keys(this.etapas);
                },

                etapaInfo(key) {
                    return this.etapasCfg[key] || { label: key, cor: '#94A3B8' };
                },

                etapaIndex(key) {
                    return this.etapaKeys.indexOf(key);
                },

                formatarData(data) {
                    if (!data) return '—';
                    const [y, m, d] = data.split('-');
                    return `${d}/${m}/${y}`;
                },
            };
        }
    </script>

</x-layouts.cliente>
