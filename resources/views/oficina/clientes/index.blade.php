<x-layouts.oficina title="Clientes">

    <script>
        window.__clientes = {!! json_encode($clientes, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    </script>

    <div x-data="clientesIndex()" x-init="init()">

        {{-- ==================== HEADER ==================== --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <h2 class="font-display font-bold text-void text-xl">Clientes</h2>
                <span class="font-mono text-xs px-2 py-0.5 rounded-full bg-ocean/10 text-ocean font-semibold"
                      x-text="clientes.length"></span>
            </div>
            <button type="button"
                    onclick="alert('Fase 1 — cadastro de clientes em breve')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-spark text-white text-sm font-medium hover:bg-spark/90 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Cliente
            </button>
        </div>

        {{-- ==================== BUSCA ==================== --}}
        <div class="relative mb-6">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none"
                 fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
            <input type="text"
                   x-model="busca"
                   placeholder="Buscar por nome, CPF, CNPJ ou telefone..."
                   class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-border bg-white text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
        </div>

        {{-- ==================== EMPTY: sem clientes ==================== --}}
        <template x-if="clientes.length === 0">
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-14 h-14 rounded-full bg-surface flex items-center justify-center mb-4"
                     style="border: 1px solid var(--color-border);">
                    <svg class="w-7 h-7 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                </div>
                <p class="font-display font-semibold text-void text-base mb-1">Nenhum cliente cadastrado ainda.</p>
                <button type="button"
                        onclick="alert('Fase 1 — cadastro de clientes em breve')"
                        class="mt-3 text-spark text-sm font-medium hover:underline">
                    + Cadastrar primeiro cliente
                </button>
            </div>
        </template>

        {{-- ==================== GRID DE CARDS ==================== --}}
        <template x-if="clientes.length > 0">
            <div>
                {{-- Empty state da busca --}}
                <div x-show="clientesFiltrados.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-14 h-14 rounded-full bg-surface flex items-center justify-center mb-4"
                         style="border: 1px solid var(--color-border);">
                        <svg class="w-7 h-7 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                    </div>
                    <p class="font-display font-semibold text-void text-base mb-1">Nenhum cliente encontrado.</p>
                    <p class="text-sm text-muted">Tente buscar por outro nome, CPF ou telefone.</p>
                </div>

                {{-- Cards --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <template x-for="cliente in clientesFiltrados" :key="cliente.id">
                        <a :href="'/oficina/clientes/' + cliente.id"
                           class="block bg-white rounded-xl p-5 hover:shadow-md transition-all group cursor-pointer"
                           style="border: 1px solid var(--color-border);">

                            <div class="flex items-start gap-4">
                                {{-- Avatar --}}
                                <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0 text-white font-display font-bold text-sm"
                                     :style="cliente.tipo === 'pj' ? 'background:#F59E0B;' : 'background:#1E3A5F;'"
                                     x-text="initiais(cliente.nome)"></div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                        <span class="font-display font-semibold text-void text-sm group-hover:text-ocean transition-colors truncate"
                                              x-text="cliente.nome"></span>
                                        <span x-show="cliente.tipo === 'pj'"
                                              class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wide flex-shrink-0"
                                              style="background: rgba(245,158,11,0.12); color: #B45309;">
                                            PJ
                                        </span>
                                    </div>

                                    <p class="text-xs text-muted mb-2">
                                        <span x-text="cliente.tipo === 'pj' ? cliente.cnpj : cliente.cpf"></span>
                                        <span class="mx-1">·</span>
                                        <span x-text="cliente.telefone"></span>
                                    </p>

                                    <div class="flex items-center gap-3 flex-wrap">
                                        {{-- Badge OS ativa --}}
                                        <template x-if="cliente.os_ativa">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold text-white"
                                                  :style="'background:' + etapasCor[cliente.os_ativa.etapa_atual]"
                                                  x-text="etapasLabel[cliente.os_ativa.etapa_atual]">
                                            </span>
                                        </template>

                                        {{-- Total OS --}}
                                        <span class="text-[11px] text-muted"
                                              x-text="cliente.total_os + (cliente.total_os === 1 ? ' OS' : ' OS')">
                                        </span>
                                    </div>
                                </div>

                                {{-- Seta --}}
                                <svg class="w-4 h-4 text-muted group-hover:text-ocean transition-colors flex-shrink-0 mt-0.5"
                                     fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </template>

    </div>

    <script>
        function clientesIndex() {
            return {
                busca: '',
                clientes: [],
                etapasCor: {},
                etapasLabel: {},

                init() {
                    this.clientes = window.__clientes || [];
                    this.etapasCor = {
                        checkin:     '#94A3B8',
                        diagnostico: '#3B82F6',
                        pecas:       '#F59E0B',
                        servico:     '#7C3AED',
                        testes:      '#06B6D4',
                        finalizacao: '#10B981',
                    };
                    this.etapasLabel = {
                        checkin:     'Check-in',
                        diagnostico: 'Diagnóstico',
                        pecas:       'Aguard. Peças',
                        servico:     'Serviço',
                        testes:      'Testes',
                        finalizacao: 'Finalização',
                    };
                },

                get clientesFiltrados() {
                    if (!this.busca.trim()) return this.clientes;
                    const q = this.busca.toLowerCase();
                    return this.clientes.filter(c =>
                        c.nome.toLowerCase().includes(q) ||
                        (c.cpf  && c.cpf.includes(q))  ||
                        (c.cnpj && c.cnpj.includes(q)) ||
                        c.telefone.includes(q)
                    );
                },

                initiais(nome) {
                    const partes = nome.trim().split(' ').filter(Boolean);
                    if (partes.length === 1) return partes[0][0].toUpperCase();
                    return (partes[0][0] + partes[partes.length - 1][0]).toUpperCase();
                },
            };
        }
    </script>

</x-layouts.oficina>
