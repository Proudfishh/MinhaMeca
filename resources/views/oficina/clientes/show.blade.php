<x-layouts.oficina :title="$cliente['nome']">

    <script>
        window.__cliente  = {!! json_encode($cliente,     JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__veiculos = {!! json_encode($veiculos,    JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__os       = {!! json_encode($osDoCliente, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__etapas   = {!! json_encode($etapas,      JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    </script>

    <div x-data="clienteDetalhe()" x-init="init()">

        {{-- ==================== BREADCRUMB ==================== --}}
        <nav class="flex items-center gap-1.5 text-xs text-muted mb-5">
            <a href="{{ route('oficina.clientes.index') }}"
               class="hover:text-void transition-colors">Clientes</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-void font-medium truncate" x-text="cliente.nome"></span>
        </nav>

        {{-- ==================== HEADER ==================== --}}
        <div class="bg-white rounded-xl p-5 sm:p-6 mb-6" style="border: 1px solid var(--color-border);">
            <div class="flex items-start justify-between gap-4 flex-wrap">

                <div class="flex items-center gap-4">
                    {{-- Avatar --}}
                    <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0 text-white font-display font-bold text-lg"
                         :style="cliente.tipo === 'pj' ? 'background:#F59E0B;' : 'background:#1E3A5F;'"
                         x-text="initiais(cliente.nome)"></div>

                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h2 class="font-display font-bold text-void text-xl" x-text="cliente.nome"></h2>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wide"
                                  :style="cliente.tipo === 'pj'
                                      ? 'background:rgba(245,158,11,0.12);color:#B45309;'
                                      : 'background:rgba(30,58,95,0.1);color:#1E3A5F;'"
                                  x-text="cliente.tipo === 'pj' ? 'PJ' : 'PF'">
                            </span>
                        </div>
                        <p class="text-sm text-muted">
                            <span x-text="cliente.tipo === 'pj' ? 'CNPJ ' + cliente.cnpj : 'CPF ' + cliente.cpf"></span>
                            <span class="mx-1.5">·</span>
                            <span x-text="cliente.telefone"></span>
                            <template x-if="cliente.email">
                                <span>
                                    <span class="mx-1.5">·</span>
                                    <span x-text="cliente.email"></span>
                                </span>
                            </template>
                        </p>
                    </div>
                </div>

                {{-- Ações --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('oficina.os.create') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-spark text-white text-sm font-medium hover:bg-spark/90 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nova OS
                    </a>
                    <button type="button"
                            onclick="alert('Fase 1 — edição disponível em breve')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-border text-void text-sm font-medium hover:bg-surface transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Editar
                    </button>
                </div>
            </div>
        </div>

        {{-- ==================== ABAS ==================== --}}
        <div class="bg-white rounded-xl" style="border: 1px solid var(--color-border);">

            {{-- Tab nav --}}
            <div class="flex border-b" style="border-color: var(--color-border);">
                <template x-for="aba in abas" :key="aba.id">
                    <button type="button"
                            @click="tab = aba.id"
                            class="px-5 py-3.5 text-sm font-medium transition-colors relative flex-shrink-0"
                            :class="tab === aba.id
                                ? 'text-void'
                                : 'text-muted hover:text-void'">
                        <span x-text="aba.label"></span>
                        <template x-if="aba.count !== null">
                            <span class="ml-1.5 font-mono text-[10px] px-1.5 py-0.5 rounded-full"
                                  :class="tab === aba.id ? 'bg-ocean/10 text-ocean' : 'bg-surface text-muted'"
                                  x-text="aba.count"></span>
                        </template>
                        <span x-show="tab === aba.id"
                              class="absolute bottom-0 left-0 right-0 h-0.5 bg-ocean rounded-t"></span>
                    </button>
                </template>
            </div>

            {{-- ======= ABA: DADOS ======= --}}
            <div x-show="tab === 'dados'" class="p-5 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">

                    <template x-if="cliente.tipo === 'pf'">
                        <div class="contents">
                            <div>
                                <p class="text-[11px] font-medium text-muted uppercase tracking-wide mb-1">Nome completo</p>
                                <p class="text-sm text-void font-medium" x-text="cliente.nome"></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium text-muted uppercase tracking-wide mb-1">CPF</p>
                                <p class="text-sm text-void font-mono" x-text="cliente.cpf"></p>
                            </div>
                        </div>
                    </template>

                    <template x-if="cliente.tipo === 'pj'">
                        <div class="contents">
                            <div>
                                <p class="text-[11px] font-medium text-muted uppercase tracking-wide mb-1">Razão Social</p>
                                <p class="text-sm text-void font-medium" x-text="cliente.nome"></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium text-muted uppercase tracking-wide mb-1">CNPJ</p>
                                <p class="text-sm text-void font-mono" x-text="cliente.cnpj"></p>
                            </div>
                        </div>
                    </template>

                    <div>
                        <p class="text-[11px] font-medium text-muted uppercase tracking-wide mb-1">Telefone</p>
                        <p class="text-sm text-void" x-text="cliente.telefone"></p>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-muted uppercase tracking-wide mb-1">E-mail</p>
                        <p class="text-sm text-void" x-text="cliente.email || '—'"></p>
                    </div>
                </div>

                <p class="text-xs text-muted mt-6 pt-4" style="border-top: 1px solid var(--color-border);">
                    Edição disponível em breve.
                </p>
            </div>

            {{-- ======= ABA: VEÍCULOS ======= --}}
            <div x-show="tab === 'veiculos'" class="p-5 sm:p-6">

                <template x-if="veiculos.length === 0">
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-surface flex items-center justify-center mb-3"
                             style="border: 1px solid var(--color-border);">
                            <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z"/>
                            </svg>
                        </div>
                        <p class="font-display font-semibold text-void text-sm mb-1">Nenhum veículo cadastrado.</p>
                        <button type="button"
                                onclick="alert('Fase 1 — adição de veículo em breve')"
                                class="mt-2 text-spark text-sm font-medium hover:underline">
                            + Adicionar veículo
                        </button>
                    </div>
                </template>

                <template x-if="veiculos.length > 0">
                    <div>
                        <div class="space-y-3 mb-4">
                            <template x-for="v in veiculos" :key="v.id">
                                <div class="flex items-center gap-4 p-4 rounded-lg bg-surface"
                                     style="border: 1px solid var(--color-border);">
                                    <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center flex-shrink-0"
                                         style="border: 1px solid var(--color-border);">
                                        <svg class="w-5 h-5 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                            <span class="font-mono text-sm font-semibold text-void" x-text="v.placa"></span>
                                            <template x-if="v.os_ativa">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold text-white"
                                                      :style="'background:' + etapasCor[v.os_ativa.etapa_atual]"
                                                      x-text="etapasLabel[v.os_ativa.etapa_atual]">
                                                </span>
                                            </template>
                                        </div>
                                        <p class="text-xs text-muted"
                                           x-text="v.marca + ' ' + v.modelo + ' ' + v.ano + ' · ' + v.cor"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <button type="button"
                                onclick="alert('Fase 1 — adição de veículo em breve')"
                                class="text-spark text-sm font-medium hover:underline">
                            + Adicionar veículo
                        </button>
                    </div>
                </template>
            </div>

            {{-- ======= ABA: HISTÓRICO DE OS ======= --}}
            <div x-show="tab === 'historico'" class="p-5 sm:p-6">

                <template x-if="os.length === 0">
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-surface flex items-center justify-center mb-3"
                             style="border: 1px solid var(--color-border);">
                            <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="font-display font-semibold text-void text-sm">Nenhuma OS encontrada.</p>
                    </div>
                </template>

                <template x-if="os.length > 0">
                    <div class="space-y-2">
                        <template x-for="item in os" :key="item.id">
                            <a :href="'/oficina/os/' + item.id"
                               class="flex items-center gap-3 p-3.5 rounded-lg bg-white hover:bg-surface transition-colors group"
                               style="border: 1px solid var(--color-border);">

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                        <span class="font-mono text-xs font-semibold text-void" x-text="item.id"></span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold text-white"
                                              :style="'background:' + etapasCor[item.etapa_atual]"
                                              x-text="etapasLabel[item.etapa_atual]">
                                        </span>
                                    </div>
                                    <p class="text-xs text-muted truncate" x-text="item.veiculo"></p>
                                </div>

                                <div class="text-right flex-shrink-0">
                                    <p class="text-xs text-muted mb-0.5" x-text="formatarData(item.data_entrada)"></p>
                                    <p class="text-sm font-semibold text-void"
                                       x-text="item.total > 0 ? 'R$ ' + item.total.toLocaleString('pt-BR', {minimumFractionDigits:2}) : '—'">
                                    </p>
                                </div>

                                <svg class="w-4 h-4 text-muted group-hover:text-ocean transition-colors flex-shrink-0"
                                     fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </template>
                    </div>
                </template>
            </div>

        </div>
    </div>

    <script>
        function clienteDetalhe() {
            return {
                tab: 'dados',
                cliente: {},
                veiculos: [],
                os: [],
                abas: [],
                etapasCor: {},
                etapasLabel: {},

                init() {
                    this.cliente  = window.__cliente  || {};
                    this.veiculos = window.__veiculos || [];
                    this.os       = window.__os       || [];

                    const etapas = window.__etapas || {};
                    this.etapasCor   = Object.fromEntries(Object.entries(etapas).map(([k, v]) => [k, v.cor]));
                    this.etapasLabel = Object.fromEntries(Object.entries(etapas).map(([k, v]) => [k, v.label]));

                    this.abas = [
                        { id: 'dados',     label: 'Dados',       count: null },
                        { id: 'veiculos',  label: 'Veículos',    count: this.veiculos.length },
                        { id: 'historico', label: 'Histórico',   count: this.os.length },
                    ];
                },

                initiais(nome) {
                    const partes = (nome || '').trim().split(' ').filter(Boolean);
                    if (partes.length === 1) return partes[0][0].toUpperCase();
                    return (partes[0][0] + partes[partes.length - 1][0]).toUpperCase();
                },

                formatarData(data) {
                    if (!data) return '—';
                    const [y, m, d] = data.split('-');
                    return `${d}/${m}/${y}`;
                },
            };
        }
    </script>

</x-layouts.oficina>
