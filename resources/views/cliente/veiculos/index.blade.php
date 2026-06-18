<x-layouts.cliente title="Meu Portal">

    <script>
        window.__ativas    = {!! json_encode($ativas,    JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__historico = {!! json_encode($historico, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__etapas    = {!! json_encode($etapas,    JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    </script>

    <div x-data="portalCliente()" x-init="init()">

        {{-- ============================================================ --}}
        {{-- BLOCO: EM ANDAMENTO --}}
        {{-- ============================================================ --}}
        <section class="mb-10">
            <h2 class="font-display font-semibold text-void text-lg mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-spark inline-block"></span>
                Em andamento
                <span class="text-muted text-sm font-normal ml-1" x-text="'(' + ativas.length + ')'"></span>
            </h2>

            {{-- Estado vazio --}}
            <template x-if="ativas.length === 0">
                <div class="bg-white rounded-xl border border-border px-6 py-12 text-center">
                    <svg class="w-10 h-10 text-border mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z"/>
                    </svg>
                    <p class="text-muted text-sm">Nenhum veículo em manutenção no momento.</p>
                </div>
            </template>

            {{-- Cards de OS ativas --}}
            <template x-for="os in ativas" :key="os.id">
                <div class="bg-white rounded-xl border border-border mb-4 overflow-hidden">

                    {{-- Cabeçalho --}}
                    <div class="px-5 py-4 flex items-start justify-between gap-3"
                         style="border-bottom: 1px solid var(--color-border);">
                        <div>
                            <p class="font-semibold text-void text-sm" x-text="os.veiculo"></p>
                            <p class="text-muted text-xs mt-0.5" x-text="'OS ' + os.id"></p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold flex-shrink-0"
                              :style="'background:' + etapaInfo(os.etapa_atual).cor + '20; color:' + etapaInfo(os.etapa_atual).cor">
                            <span x-text="etapaInfo(os.etapa_atual).label"></span>
                        </span>
                    </div>

                    {{-- Barra de progresso --}}
                    <div class="px-5 pt-4 pb-3">
                        <div class="flex items-center gap-0">
                            <template x-for="(etapa, idx) in etapas" :key="etapa">
                                <div class="flex items-center flex-1 min-w-0">
                                    {{-- Círculo da etapa --}}
                                    <div class="flex flex-col items-center flex-shrink-0">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center transition-all"
                                             :class="{
                                                 'bg-spark text-white ring-2 ring-spark ring-offset-2': os.etapa_atual === etapa,
                                                 'bg-spark/20': etapaIndex(os.etapa_atual) > idx,
                                                 'bg-border': etapaIndex(os.etapa_atual) < idx
                                             }">
                                            <template x-if="etapaIndex(os.etapa_atual) > idx">
                                                <svg class="w-3 h-3 text-spark" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </template>
                                            <template x-if="os.etapa_atual === etapa">
                                                <div class="w-2 h-2 rounded-full bg-white"></div>
                                            </template>
                                        </div>
                                        <span class="text-[9px] mt-1 text-center leading-tight w-12 truncate"
                                              :class="os.etapa_atual === etapa ? 'text-spark font-semibold' : 'text-muted'"
                                              x-text="etapaInfo(etapa).label"></span>
                                    </div>
                                    {{-- Linha conectora --}}
                                    <template x-if="idx < etapas.length - 1">
                                        <div class="flex-1 h-0.5 mx-1 mb-4 transition-all"
                                             :class="etapaIndex(os.etapa_atual) > idx ? 'bg-spark/40' : 'bg-border'">
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Info: previsão + descrição --}}
                    <div class="px-5 pb-4 space-y-2">
                        <template x-if="os.previsao_entrega">
                            <div class="flex items-center gap-1.5 text-xs text-muted">
                                <svg class="w-3.5 h-3.5 text-muted flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Previsão de entrega: <strong class="text-void" x-text="formatarData(os.previsao_entrega)"></strong></span>
                            </div>
                        </template>

                        <template x-if="os.descricao_cliente">
                            <div class="flex items-start gap-1.5 text-xs text-muted">
                                <svg class="w-3.5 h-3.5 text-muted flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                <span x-text="os.descricao_cliente"></span>
                            </div>
                        </template>
                    </div>

                    {{-- Lista de serviços --}}
                    <template x-if="os.servicos && os.servicos.length > 0">
                        <div style="border-top: 1px solid var(--color-border);">
                            <div class="px-5 py-3">
                                <p class="text-xs font-semibold text-muted uppercase tracking-wide mb-2">Serviços</p>
                                <div class="space-y-2">
                                    <template x-for="(srv, i) in os.servicos" :key="i">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2 min-w-0">
                                                {{-- Ícone de status --}}
                                                <template x-if="srv.status === 'concluido'">
                                                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </template>
                                                <template x-if="srv.status === 'em_andamento'">
                                                    <svg class="w-4 h-4 text-spark flex-shrink-0 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                    </svg>
                                                </template>
                                                <template x-if="srv.status === 'pendente'">
                                                    <div class="w-4 h-4 rounded-full border-2 border-border flex-shrink-0"></div>
                                                </template>
                                                <span class="text-xs text-void truncate" x-text="srv.descricao"></span>
                                            </div>
                                            <span class="text-xs font-mono text-muted flex-shrink-0" x-text="formatarValor(srv.valor)"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            {{-- Total --}}
                            <div class="px-5 py-3 flex justify-between items-center"
                                 style="border-top: 1px solid var(--color-border); background: rgba(248,250,252,0.8);">
                                <span class="text-xs font-semibold text-muted uppercase tracking-wide">Total estimado</span>
                                <span class="text-sm font-bold text-void font-mono" x-text="formatarValor(os.total)"></span>
                            </div>
                        </div>
                    </template>

                </div>
            </template>
        </section>

        {{-- ============================================================ --}}
        {{-- BLOCO: HISTÓRICO --}}
        {{-- ============================================================ --}}
        <section>
            <h2 class="font-display font-semibold text-void text-lg mb-4 flex items-center gap-2">
                <svg class="w-4.5 h-4.5 text-muted" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Histórico
                <span class="text-muted text-sm font-normal ml-1" x-text="'(' + historico.length + ')'"></span>
            </h2>

            <template x-if="historico.length === 0">
                <div class="bg-white rounded-xl border border-border px-6 py-10 text-center">
                    <p class="text-muted text-sm">Nenhum serviço anterior registrado.</p>
                </div>
            </template>

            <template x-for="os in historico" :key="os.id">
                <div class="bg-white rounded-xl border border-border mb-3 overflow-hidden"
                     x-data="{ aberto: false }">

                    {{-- Cabeçalho compacto (sempre visível) --}}
                    <button @click="aberto = !aberto"
                            class="w-full px-5 py-4 flex items-center justify-between gap-3 text-left hover:bg-surface/50 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                 style="background: rgba(16,185,129,0.1);">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-void truncate" x-text="os.veiculo"></p>
                                <p class="text-xs text-muted mt-0.5">
                                    <span x-text="formatarData(os.data_entrega_real)"></span>
                                    &middot;
                                    <span x-text="(os.servicos ? os.servicos.length : 0) + ' serviço(s)'"></span>
                                    &middot;
                                    <span class="font-mono" x-text="formatarValor(os.total)"></span>
                                </p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-muted flex-shrink-0 transition-transform"
                             :class="aberto ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Detalhes expandidos --}}
                    <div x-show="aberto"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         style="border-top: 1px solid var(--color-border);">

                        <div class="px-5 py-4 space-y-3">

                            {{-- Mécanico e datas --}}
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div>
                                    <p class="text-muted uppercase tracking-wide text-[10px] font-semibold mb-0.5">Mecânico</p>
                                    <p class="text-void" x-text="os.mecanico || '—'"></p>
                                </div>
                                <div>
                                    <p class="text-muted uppercase tracking-wide text-[10px] font-semibold mb-0.5">Entrada</p>
                                    <p class="text-void" x-text="formatarData(os.data_entrada)"></p>
                                </div>
                            </div>

                            {{-- Descrição --}}
                            <template x-if="os.descricao_cliente">
                                <div>
                                    <p class="text-muted uppercase tracking-wide text-[10px] font-semibold mb-0.5">Problema relatado</p>
                                    <p class="text-xs text-void" x-text="os.descricao_cliente"></p>
                                </div>
                            </template>

                            {{-- Serviços --}}
                            <template x-if="os.servicos && os.servicos.length > 0">
                                <div>
                                    <p class="text-muted uppercase tracking-wide text-[10px] font-semibold mb-2">Serviços realizados</p>
                                    <div class="space-y-1.5">
                                        <template x-for="(srv, i) in os.servicos" :key="i">
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-void" x-text="srv.descricao"></span>
                                                <span class="font-mono text-muted" x-text="formatarValor(srv.valor)"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                        </div>

                        {{-- Total --}}
                        <div class="px-5 py-3 flex justify-between items-center"
                             style="border-top: 1px solid var(--color-border); background: rgba(248,250,252,0.8);">
                            <span class="text-xs font-semibold text-muted uppercase tracking-wide">Total pago</span>
                            <span class="text-sm font-bold text-void font-mono" x-text="formatarValor(os.total)"></span>
                        </div>

                    </div>
                </div>
            </template>
        </section>

    </div>

    <script>
        function portalCliente() {
            return {
                ativas:    [],
                historico: [],
                etapas:    [],

                etapasCfg: {
                    checkin:     { label: 'Check-in',     cor: '#94A3B8' },
                    diagnostico: { label: 'Diagnóstico',  cor: '#3B82F6' },
                    pecas:       { label: 'Peças',        cor: '#F59E0B' },
                    servico:     { label: 'Serviço',      cor: '#7C3AED' },
                    testes:      { label: 'Testes',       cor: '#06B6D4' },
                    finalizacao: { label: 'Finalização',  cor: '#10B981' },
                },

                init() {
                    this.ativas    = window.__ativas    || [];
                    this.historico = window.__historico || [];
                    this.etapas    = window.__etapas    || [];
                },

                etapaInfo(etapa) {
                    return this.etapasCfg[etapa] || { label: etapa, cor: '#94A3B8' };
                },

                etapaIndex(etapa) {
                    return this.etapas.indexOf(etapa);
                },

                formatarData(data) {
                    if (!data) return '—';
                    const [y, m, d] = data.split('-');
                    return `${d}/${m}/${y}`;
                },

                formatarValor(valor) {
                    if (valor === undefined || valor === null) return '—';
                    return 'R$ ' + Number(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
            };
        }
    </script>

</x-layouts.cliente>
