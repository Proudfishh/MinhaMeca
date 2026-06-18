<x-layouts.oficina title="Pendências">

    <script>
        window.__pendencias = {!! json_encode($pendencias, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__metricas   = {!! json_encode($metricas,   JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__osList     = {!! json_encode($osList,     JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    </script>

    <div x-data="pendenciasPage()" x-init="init()" @keydown.escape.window="fecharModais()">

        {{-- ==================== HEADER ==================== --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <h2 class="font-display font-bold text-void text-xl">Pendências</h2>
                <span class="font-mono text-xs px-2 py-0.5 rounded-full bg-spark/10 text-spark font-semibold"
                      x-text="metricas.ativas + ' ativas'"></span>
            </div>
            <button type="button" @click="abrirModalNova()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-spark text-white text-sm font-medium hover:bg-spark/90 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Pendência
            </button>
        </div>

        {{-- ==================== MÉTRICAS ==================== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
                <p class="text-xs font-medium text-muted uppercase tracking-wide mb-2">Em aberto</p>
                <p class="font-display font-bold text-void text-xl"
                   x-text="formatarMoeda(metricas.em_aberto)"></p>
                <p class="text-[10px] text-muted mt-1">pendente + parcial</p>
            </div>
            <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
                <p class="text-xs font-medium text-muted uppercase tracking-wide mb-2">Vencido</p>
                <p class="font-display font-bold text-xl"
                   :class="metricas.vencido > 0 ? 'text-red-500' : 'text-void'"
                   x-text="formatarMoeda(metricas.vencido)"></p>
                <p class="text-[10px] text-muted mt-1">não pago após vencimento</p>
            </div>
            <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
                <p class="text-xs font-medium text-muted uppercase tracking-wide mb-2">Recebido este mês</p>
                <p class="font-display font-bold text-void text-xl"
                   x-text="formatarMoeda(metricas.recebido_mes)"></p>
                <p class="text-[10px] text-muted mt-1">pagamentos registrados</p>
            </div>
            <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
                <p class="text-xs font-medium text-muted uppercase tracking-wide mb-2">Pendências ativas</p>
                <p class="font-display font-bold text-void text-xl" x-text="metricas.ativas"></p>
                <p class="text-[10px] text-muted mt-1">excluindo pagas</p>
            </div>
        </div>

        {{-- ==================== FILTROS + BUSCA ==================== --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-5">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none"
                     fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                <input type="text" x-model="busca"
                       placeholder="Buscar por cliente, descrição ou ID..."
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
                        <span x-show="f.count !== null"
                              class="ml-1 font-mono" x-text="'(' + f.count + ')'"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- ==================== EMPTY STATE ==================== --}}
        <template x-if="pendenciasFiltradas.length === 0">
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-14 h-14 rounded-full bg-surface flex items-center justify-center mb-4"
                     style="border: 1px solid var(--color-border);">
                    <svg class="w-7 h-7 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="font-display font-semibold text-void text-base mb-1"
                   x-text="busca || filtro !== 'todas' ? 'Nenhuma pendência encontrada.' : 'Nenhuma pendência registrada.'"></p>
                <template x-if="!busca && filtro === 'todas'">
                    <button type="button" @click="abrirModalNova()"
                            class="mt-3 text-spark text-sm font-medium hover:underline">
                        + Criar primeira pendência
                    </button>
                </template>
            </div>
        </template>

        {{-- ==================== LISTA DE PENDÊNCIAS ==================== --}}
        <div class="space-y-4">
            <template x-for="p in pendenciasFiltradas" :key="p.id">
                <div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid var(--color-border);">

                    {{-- Cabeçalho do card --}}
                    <div class="p-5 pb-4">
                        <div class="flex items-start justify-between gap-3 flex-wrap mb-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold text-white"
                                      :style="'background:' + statusCor(p.status)"
                                      x-text="statusLabel(p.status)"></span>
                                <span class="font-mono text-xs font-semibold text-void" x-text="p.id"></span>
                                <span class="text-xs text-muted">· criado em <span x-text="formatarData(p.data_criacao)"></span></span>
                            </div>
                            <div class="text-right">
                                <p class="font-display font-bold text-void text-base"
                                   x-text="formatarMoeda(p.valor_total)"></p>
                                <p x-show="p.valor_pago < p.valor_total"
                                   class="text-xs text-muted"
                                   x-text="formatarMoeda(p.valor_total - p.valor_pago) + ' em aberto'"></p>
                                <p x-show="p.valor_pago >= p.valor_total"
                                   class="text-xs font-medium" style="color:#10B981;">Quitado</p>
                            </div>
                        </div>

                        <p class="font-medium text-void text-sm mb-0.5" x-text="p.cliente"></p>
                        <div class="flex items-center gap-2 text-xs text-muted flex-wrap">
                            <template x-if="p.os_id">
                                <a :href="'/oficina/os/' + p.os_id"
                                   class="text-ocean hover:underline font-medium" x-text="p.os_id" @click.stop></a>
                            </template>
                            <template x-if="!p.os_id">
                                <span class="px-1.5 py-0.5 rounded text-[10px] bg-surface border border-border">Avulso</span>
                            </template>
                            <span class="text-muted/50">·</span>
                            <span x-text="p.descricao" class="truncate"></span>
                        </div>
                    </div>

                    {{-- Parcelas --}}
                    <div class="border-t" style="border-color: var(--color-border);">
                        <template x-for="parc in p.parcelas" :key="parc.numero">
                            <div class="flex items-center justify-between gap-3 px-5 py-3 border-b last:border-b-0"
                                 style="border-color: var(--color-border);">
                                <div class="flex items-center gap-3 min-w-0">
                                    {{-- Ícone paga / aberta --}}
                                    <template x-if="parc.pago_em">
                                        <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0"
                                             style="background:rgba(16,185,129,0.12);">
                                            <svg class="w-3 h-3" fill="none" stroke="#10B981" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    </template>
                                    <template x-if="!parc.pago_em">
                                        <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 bg-surface"
                                             style="border: 1.5px solid var(--color-border);">
                                        </div>
                                    </template>

                                    <div class="min-w-0">
                                        <span class="text-xs font-medium text-void"
                                              x-text="'Parcela ' + parc.numero + '  ·  ' + formatarMoeda(parc.valor)"></span>
                                        <span x-show="parc.pago_em"
                                              class="ml-2 text-xs text-muted"
                                              x-text="'pago em ' + formatarData(parc.pago_em) + (parc.forma_pagamento ? ' · ' + parc.forma_pagamento : '')"></span>
                                        <span x-show="!parc.pago_em"
                                              class="ml-2 text-xs"
                                              :class="estaVencida(parc.vencimento) ? 'text-red-500 font-medium' : 'text-muted'"
                                              x-text="(estaVencida(parc.vencimento) ? 'vencida em ' : 'vence em ') + formatarData(parc.vencimento)"></span>
                                    </div>
                                </div>

                                <button x-show="!parc.pago_em"
                                        type="button"
                                        @click="abrirModalPagamento(p.id, parc.numero, parc.valor)"
                                        class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-spark/10 text-spark hover:bg-spark/20 border border-spark/20 hover:border-spark/40 transition-all whitespace-nowrap">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Registrar
                                </button>
                            </div>
                        </template>
                    </div>

                </div>
            </template>
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

        {{-- ==================== MODAL REGISTRAR PAGAMENTO ==================== --}}
        <template x-teleport="body">
            <div x-show="mpAberto"
                 class="fixed inset-0 z-40 flex items-center justify-center p-4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-void/50" @click="fecharModais()"></div>
                <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-xl"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     @click.stop>

                    <div class="flex items-center justify-between px-6 py-4" style="border-bottom: 1px solid var(--color-border);">
                        <h3 class="font-display font-semibold text-void text-base">Registrar pagamento</h3>
                        <button type="button" @click="fecharModais()"
                                class="text-muted hover:text-void transition-colors p-1 rounded-lg hover:bg-surface">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Valor recebido</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted">R$</span>
                                <input type="number" step="0.01" x-model="mpValor"
                                       class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                            </div>
                            <p x-show="mpValorOriginal > 0"
                               class="text-[10px] text-muted mt-1"
                               x-text="'Valor original da parcela: ' + formatarMoeda(mpValorOriginal)"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Data do pagamento</label>
                            <input type="date" x-model="mpData"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Forma de pagamento</label>
                            <select x-model="mpForma"
                                    class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                <option value="Pix">Pix</option>
                                <option value="Dinheiro">Dinheiro</option>
                                <option value="Cartão de débito">Cartão de débito</option>
                                <option value="Cartão de crédito">Cartão de crédito</option>
                                <option value="Boleto">Boleto</option>
                            </select>
                        </div>

                        {{-- Saldo restante — aparece quando valor pago < valor original --}}
                        <div x-show="mpSaldoRestante > 0"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="rounded-xl overflow-hidden"
                             style="border: 1.5px solid #F59E0B;">

                            <div class="flex items-center gap-2 px-4 py-2.5 bg-amber-50">
                                <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                                <p class="text-xs font-semibold text-amber-700">
                                    Saldo restante: <span x-text="formatarMoeda(mpSaldoRestante)"></span>
                                </p>
                            </div>

                            <div class="px-4 py-3 space-y-2 bg-white">
                                <p class="text-xs text-muted mb-3">O que fazer com o valor restante?</p>

                                {{-- Opção agendar --}}
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <div class="mt-0.5 w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors"
                                         :style="mpOpcaoRestante === 'agendar'
                                             ? 'border-color:#F59E0B; background:#F59E0B;'
                                             : 'border-color: var(--color-border); background: white;'"
                                         @click="mpOpcaoRestante = 'agendar'">
                                        <div x-show="mpOpcaoRestante === 'agendar'"
                                             class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                    </div>
                                    <div class="flex-1" @click="mpOpcaoRestante = 'agendar'">
                                        <p class="text-xs font-medium text-void">Agendar pagamento do restante</p>
                                        <p class="text-[10px] text-muted">Cria uma nova parcela com o saldo em aberto</p>
                                        <input x-show="mpOpcaoRestante === 'agendar'"
                                               type="date" x-model="mpDataRestante"
                                               @click.stop
                                               class="mt-2 w-full px-3 py-2 rounded-lg border border-border bg-surface text-xs text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                    </div>
                                </label>

                                {{-- Opção quitar --}}
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <div class="mt-0.5 w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors"
                                         :style="mpOpcaoRestante === 'quitar'
                                             ? 'border-color:#10B981; background:#10B981;'
                                             : 'border-color: var(--color-border); background: white;'"
                                         @click="mpOpcaoRestante = 'quitar'">
                                        <div x-show="mpOpcaoRestante === 'quitar'"
                                             class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                    </div>
                                    <div @click="mpOpcaoRestante = 'quitar'">
                                        <p class="text-xs font-medium text-void">Dar como quitado</p>
                                        <p class="text-[10px] text-muted">Aceita o valor parcial como pagamento total — saldo de <span x-text="formatarMoeda(mpSaldoRestante)"></span> é descartado</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 px-6 py-4" style="border-top: 1px solid var(--color-border);">
                        <button type="button" @click="fecharModais()"
                                class="flex-1 px-4 py-2 rounded-lg border border-border text-void text-sm font-medium hover:bg-surface transition-colors">
                            Cancelar
                        </button>
                        <button type="button" @click="confirmarPagamento()"
                                :disabled="!mpConfirmarHabilitado"
                                :class="mpConfirmarHabilitado ? 'bg-spark text-white hover:bg-spark/90' : 'bg-border text-muted cursor-not-allowed'"
                                class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ==================== MODAL NOVA PENDÊNCIA ==================== --}}
        <template x-teleport="body">
            <div x-show="modalNova"
                 class="fixed inset-0 z-40 flex items-center justify-center p-4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-void/50" @click="fecharModais()"></div>

                <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     @click.stop>

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 flex-shrink-0" style="border-bottom: 1px solid var(--color-border);">
                        <div>
                            <h3 class="font-display font-semibold text-void text-base">Nova Pendência</h3>
                            <p class="text-xs text-muted mt-0.5"
                               x-text="etapa === 1 ? 'Etapa 1 de 2 — Dados gerais' : 'Etapa 2 de 2 — Parcelamento'"></p>
                        </div>
                        <button type="button" @click="fecharModais()"
                                class="text-muted hover:text-void transition-colors p-1 rounded-lg hover:bg-surface">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Body scrollável --}}
                    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

                        {{-- ===== ETAPA 1 ===== --}}
                        <div x-show="etapa === 1">

                            {{-- Toggle OS / Avulso --}}
                            <div class="flex gap-2 mb-4">
                                <button type="button" @click="nf.tipo = 'os'; nf.os_selecionada = null; nf.os_busca = ''"
                                        class="px-3 py-1.5 rounded-full text-xs font-medium border transition-all"
                                        :class="nf.tipo === 'os'
                                            ? 'bg-spark text-white border-spark'
                                            : 'bg-white text-muted border-border hover:border-spark/40 hover:text-void'">
                                    Vinculada a OS
                                </button>
                                <button type="button" @click="nf.tipo = 'avulso'"
                                        class="px-3 py-1.5 rounded-full text-xs font-medium border transition-all"
                                        :class="nf.tipo === 'avulso'
                                            ? 'bg-spark text-white border-spark'
                                            : 'bg-white text-muted border-border hover:border-spark/40 hover:text-void'">
                                    Avulsa
                                </button>
                            </div>

                            {{-- Modo OS --}}
                            <div x-show="nf.tipo === 'os'" class="space-y-4">
                                <div class="relative">
                                    <label class="block text-xs font-medium text-muted mb-1.5">Buscar OS <span class="text-spark">*</span></label>
                                    <input type="text" x-model="nf.os_busca"
                                           placeholder="ID da OS ou nome do cliente..."
                                           class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                    {{-- Dropdown resultados --}}
                                    <div x-show="nf.os_busca.length > 0 && !nf.os_selecionada && osListFiltrada.length > 0"
                                         class="absolute top-full left-0 right-0 mt-1 bg-white rounded-lg shadow-lg z-10 overflow-hidden"
                                         style="border: 1px solid var(--color-border);">
                                        <template x-for="os in osListFiltrada" :key="os.id">
                                            <button type="button"
                                                    @click="selecionarOs(os)"
                                                    class="w-full flex items-center justify-between px-3 py-2.5 hover:bg-surface transition-colors text-left">
                                                <div>
                                                    <span class="font-mono text-xs font-semibold text-void" x-text="os.id"></span>
                                                    <span class="text-xs text-muted ml-2" x-text="os.cliente"></span>
                                                </div>
                                                <span class="text-xs font-medium text-void" x-text="formatarMoeda(os.total)"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                {{-- OS selecionada --}}
                                <div x-show="nf.os_selecionada" class="p-3 rounded-lg bg-surface" style="border: 1px solid var(--color-border);">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-mono text-xs font-semibold text-void" x-text="nf.os_selecionada?.id"></span>
                                        <button type="button" @click="nf.os_selecionada = null; nf.os_busca = ''"
                                                class="text-xs text-muted hover:text-red-500 transition-colors">remover</button>
                                    </div>
                                    <p class="text-xs text-void" x-text="nf.os_selecionada?.cliente"></p>
                                    <p class="text-xs text-muted mt-0.5">
                                        Total da OS: <span class="font-semibold text-void" x-text="formatarMoeda(nf.os_selecionada?.total)"></span>
                                    </p>
                                </div>

                                <div x-show="nf.os_selecionada">
                                    <label class="block text-xs font-medium text-muted mb-1.5">Valor já pago na entrega</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted">R$</span>
                                        <input type="number" step="0.01" min="0" x-model="nf.ja_pago"
                                               placeholder="0,00"
                                               class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                    </div>
                                    <p class="text-xs text-muted mt-1">
                                        Saldo a parcelar:
                                        <span class="font-semibold text-void" x-text="formatarMoeda(valorAParcerlar)"></span>
                                    </p>
                                </div>
                            </div>

                            {{-- Modo Avulso --}}
                            <div x-show="nf.tipo === 'avulso'" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-muted mb-1.5">Cliente <span class="text-spark">*</span></label>
                                    <input type="text" x-model="nf.cliente_nome"
                                           placeholder="Nome do cliente ou empresa..."
                                           class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-muted mb-1.5">Valor <span class="text-spark">*</span></label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted">R$</span>
                                        <input type="number" step="0.01" min="0.01" x-model="nf.valor_avulso"
                                               placeholder="0,00"
                                               class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                    </div>
                                </div>
                            </div>

                            {{-- Descrição (comum) --}}
                            <div>
                                <label class="block text-xs font-medium text-muted mb-1.5">
                                    Descrição
                                    <span x-show="nf.tipo === 'avulso'" class="text-spark">*</span>
                                </label>
                                <input type="text" x-model="nf.descricao"
                                       :placeholder="nf.tipo === 'os' ? 'Preenchida automaticamente ao selecionar a OS' : 'Ex: Adiantamento para peças...'"
                                       class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                            </div>
                        </div>

                        {{-- ===== ETAPA 2 ===== --}}
                        <div x-show="etapa === 2" class="space-y-4">
                            <div class="flex items-center justify-between p-3 rounded-lg bg-surface" style="border: 1px solid var(--color-border);">
                                <span class="text-xs text-muted">Valor a parcelar</span>
                                <span class="font-display font-bold text-void" x-text="formatarMoeda(valorAParcerlar)"></span>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-muted mb-1.5">Número de parcelas</label>
                                <select x-model="nf.num_parcelas" @change="gerarParcelas()"
                                        class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                    <template x-for="n in 12" :key="n">
                                        <option :value="n" x-text="n + (n === 1 ? ' parcela' : ' parcelas')"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <template x-for="parc in nf.parcelas" :key="parc.numero">
                                    <div class="grid grid-cols-5 gap-2 items-end">
                                        <div class="col-span-1">
                                            <label class="block text-[10px] font-medium text-muted mb-1"
                                                   x-text="'Parcela ' + parc.numero"></label>
                                            <div class="relative">
                                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-muted">R$</span>
                                                <input type="number" step="0.01" x-model="parc.valor"
                                                       class="w-full pl-7 pr-2 py-2 rounded-lg border border-border bg-surface text-xs text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                            </div>
                                        </div>
                                        <div class="col-span-4">
                                            <label class="block text-[10px] font-medium text-muted mb-1">Vencimento</label>
                                            <input type="date" x-model="parc.vencimento"
                                                   class="w-full px-3 py-2 rounded-lg border border-border bg-surface text-xs text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="flex gap-3 px-6 py-4 flex-shrink-0" style="border-top: 1px solid var(--color-border);">
                        <button type="button"
                                @click="etapa === 1 ? fecharModais() : etapa = 1"
                                class="flex-1 px-4 py-2 rounded-lg border border-border text-void text-sm font-medium hover:bg-surface transition-colors"
                                x-text="etapa === 1 ? 'Cancelar' : '← Voltar'">
                        </button>
                        <button type="button"
                                x-show="etapa === 1"
                                @click="avancarParaEtapa2()"
                                :disabled="!etapa1Valida"
                                :class="etapa1Valida ? 'bg-spark text-white hover:bg-spark/90' : 'bg-border text-muted cursor-not-allowed'"
                                class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Avançar →
                        </button>
                        <button type="button"
                                x-show="etapa === 2"
                                @click="criarPendencia()"
                                :disabled="nf.parcelas.length === 0"
                                :class="nf.parcelas.length > 0 ? 'bg-spark text-white hover:bg-spark/90' : 'bg-border text-muted cursor-not-allowed'"
                                class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Criar Pendência
                        </button>
                    </div>

                </div>
            </div>
        </template>

    </div>

    <script>
        function pendenciasPage() {
            return {
                pendencias: [],
                metricas:   {},
                osList:     [],
                filtro:     'todas',
                busca:      '',

                // Modal registrar pagamento
                mpAberto:        false,
                mpPendenciaId:   null,
                mpParcelaNum:    null,
                mpValor:         0,
                mpValorOriginal: 0,
                mpData:          '',
                mpForma:         'Pix',
                mpOpcaoRestante: 'agendar',
                mpDataRestante:  '',

                // Modal nova pendência
                modalNova: false,
                etapa:     1,
                nf: {
                    tipo:          'os',
                    os_selecionada: null,
                    os_busca:       '',
                    ja_pago:        '',
                    cliente_nome:   '',
                    valor_avulso:   '',
                    descricao:      '',
                    num_parcelas:   1,
                    parcelas:       [],
                },

                toastVisible: false,
                toastMsg:     '',
                _toastTimer:  null,

                statusCfg: {
                    pendente:  { cor: '#94A3B8', label: 'Pendente' },
                    parcial:   { cor: '#3B82F6', label: 'Parcial' },
                    vencido:   { cor: '#EF4444', label: 'Vencido' },
                    pago:      { cor: '#10B981', label: 'Pago' },
                    negociado: { cor: '#F59E0B', label: 'Negociado' },
                },

                init() {
                    this.pendencias = window.__pendencias || [];
                    this.metricas   = window.__metricas   || {};
                    this.osList     = window.__osList     || [];
                    this.mpData     = new Date().toISOString().split('T')[0];
                },

                // ── Computed ────────────────────────────────────────────────

                get filtros() {
                    const conta = (status) => this.pendencias.filter(p => {
                        if (status === 'aberto') return ['pendente','parcial'].includes(p.status);
                        if (status === 'vencidas') return p.status === 'vencido';
                        if (status === 'pagas') return p.status === 'pago';
                        if (status === 'negociadas') return p.status === 'negociado';
                        return true;
                    }).length;
                    return [
                        { valor: 'todas',      label: 'Todas',      count: null },
                        { valor: 'aberto',     label: 'Em aberto',  count: conta('aberto') },
                        { valor: 'vencidas',   label: 'Vencidas',   count: conta('vencidas') },
                        { valor: 'negociadas', label: 'Negociadas', count: conta('negociadas') },
                        { valor: 'pagas',      label: 'Pagas',      count: conta('pagas') },
                    ];
                },

                get pendenciasFiltradas() {
                    const order = { vencido: 0, parcial: 1, pendente: 2, negociado: 3, pago: 4 };
                    let r = [...this.pendencias];

                    if (this.filtro === 'aberto')     r = r.filter(p => ['pendente','parcial'].includes(p.status));
                    else if (this.filtro === 'vencidas')   r = r.filter(p => p.status === 'vencido');
                    else if (this.filtro === 'pagas')      r = r.filter(p => p.status === 'pago');
                    else if (this.filtro === 'negociadas') r = r.filter(p => p.status === 'negociado');

                    if (this.busca.trim()) {
                        const q = this.busca.toLowerCase();
                        r = r.filter(p =>
                            p.cliente.toLowerCase().includes(q) ||
                            p.descricao.toLowerCase().includes(q) ||
                            p.id.toLowerCase().includes(q)
                        );
                    }

                    return r.sort((a, b) => {
                        const d = (order[a.status] ?? 5) - (order[b.status] ?? 5);
                        if (d !== 0) return d;
                        const vA = a.parcelas.find(p => !p.pago_em)?.vencimento || '9999';
                        const vB = b.parcelas.find(p => !p.pago_em)?.vencimento || '9999';
                        return vA.localeCompare(vB);
                    });
                },

                get osListFiltrada() {
                    if (!this.nf.os_busca.trim()) return [];
                    const q = this.nf.os_busca.toLowerCase();
                    return this.osList.filter(os =>
                        os.id.toLowerCase().includes(q) ||
                        os.cliente.toLowerCase().includes(q)
                    ).slice(0, 5);
                },

                get valorAParcerlar() {
                    if (this.nf.tipo === 'os' && this.nf.os_selecionada) {
                        return Math.max(0, this.nf.os_selecionada.total - (parseFloat(this.nf.ja_pago) || 0));
                    }
                    return parseFloat(this.nf.valor_avulso) || 0;
                },

                get etapa1Valida() {
                    if (this.nf.tipo === 'os') return this.nf.os_selecionada !== null && this.valorAParcerlar > 0;
                    return this.nf.cliente_nome.trim() !== '' &&
                           parseFloat(this.nf.valor_avulso) > 0 &&
                           this.nf.descricao.trim() !== '';
                },

                get mpSaldoRestante() {
                    const pago = parseFloat(this.mpValor) || 0;
                    const diff = Math.round((this.mpValorOriginal - pago) * 100) / 100;
                    return diff > 0 ? diff : 0;
                },

                get mpConfirmarHabilitado() {
                    if (!this.mpValor || !this.mpData) return false;
                    if (this.mpSaldoRestante > 0) {
                        if (this.mpOpcaoRestante === 'agendar') return !!this.mpDataRestante;
                        return this.mpOpcaoRestante === 'quitar';
                    }
                    return true;
                },

                // ── Helpers ──────────────────────────────────────────────────

                statusCor(s)   { return this.statusCfg[s]?.cor   || '#94A3B8'; },
                statusLabel(s) { return this.statusCfg[s]?.label || s; },

                estaVencida(vencimento) {
                    return vencimento < new Date().toISOString().split('T')[0];
                },

                calcularStatus(parcelas) {
                    const hoje   = new Date().toISOString().split('T')[0];
                    const pagas  = parcelas.filter(p => p.pago_em).length;
                    const total  = parcelas.length;
                    const venc   = parcelas.filter(p => !p.pago_em && p.vencimento < hoje).length;
                    if (pagas === total) return 'pago';
                    if (pagas > 0)       return 'parcial';
                    if (venc > 0)        return 'vencido';
                    return 'pendente';
                },

                formatarMoeda(v) {
                    if (v === null || v === undefined) return '—';
                    return 'R$ ' + parseFloat(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                },

                formatarData(d) {
                    if (!d) return '—';
                    const [y, m, dd] = d.split('-');
                    return `${dd}/${m}/${y}`;
                },

                // ── Ações — pagamento ────────────────────────────────────────

                abrirModalPagamento(pendenciaId, parcelaNum, valor) {
                    this.mpPendenciaId   = pendenciaId;
                    this.mpParcelaNum    = parcelaNum;
                    this.mpValor         = valor;
                    this.mpValorOriginal = valor;
                    this.mpData          = new Date().toISOString().split('T')[0];
                    this.mpForma         = 'Pix';
                    this.mpOpcaoRestante = 'agendar';
                    this.mpDataRestante  = '';
                    this.mpAberto        = true;
                },

                confirmarPagamento() {
                    const p = this.pendencias.find(p => p.id === this.mpPendenciaId);
                    if (!p) return;
                    const parc = p.parcelas.find(pa => pa.numero === this.mpParcelaNum);
                    if (!parc) return;

                    const valorPago = parseFloat(this.mpValor) || parc.valor;
                    const saldo     = this.mpSaldoRestante;

                    if (saldo > 0 && this.mpOpcaoRestante === 'agendar') {
                        // Registra valor parcial na parcela atual
                        parc.valor           = valorPago;
                        parc.pago_em         = this.mpData;
                        parc.forma_pagamento = this.mpForma;

                        // Cria nova parcela com o saldo restante
                        const maxNum = Math.max(...p.parcelas.map(pa => pa.numero));
                        p.parcelas.push({
                            numero:          maxNum + 1,
                            valor:           saldo,
                            vencimento:      this.mpDataRestante,
                            pago_em:         null,
                            forma_pagamento: null,
                        });
                        p.valor_total = p.parcelas.reduce((s, pa) => s + pa.valor, 0);

                    } else if (saldo > 0 && this.mpOpcaoRestante === 'quitar') {
                        // Aceita valor parcial como quitação — reduz o valor da parcela
                        parc.valor           = valorPago;
                        parc.pago_em         = this.mpData;
                        parc.forma_pagamento = this.mpForma;
                        p.valor_total        = p.parcelas.reduce((s, pa) => s + pa.valor, 0);

                    } else {
                        // Pagamento integral
                        parc.pago_em         = this.mpData;
                        parc.forma_pagamento = this.mpForma;
                        parc.valor           = valorPago;
                    }

                    p.valor_pago = p.parcelas.filter(pa => pa.pago_em)
                        .reduce((s, pa) => s + pa.valor, 0);
                    if (p.status !== 'negociado') {
                        p.status = this.calcularStatus(p.parcelas);
                    }

                    this.recalcularMetricas();
                    this.mpAberto = false;

                    const msg = saldo > 0 && this.mpOpcaoRestante === 'agendar'
                        ? 'Pagamento parcial registrado — nova parcela criada!'
                        : 'Pagamento registrado com sucesso!';
                    this.mostrarToast(msg);
                },

                recalcularMetricas() {
                    const hoje = new Date().toISOString().split('T')[0];
                    const mesAtual = hoje.substring(0, 7);
                    this.metricas.em_aberto = this.pendencias
                        .filter(p => ['pendente','parcial'].includes(p.status))
                        .reduce((s, p) => s + (p.valor_total - p.valor_pago), 0);
                    this.metricas.vencido = this.pendencias
                        .filter(p => p.status === 'vencido')
                        .reduce((s, p) => s + (p.valor_total - p.valor_pago), 0);
                    this.metricas.recebido_mes = this.pendencias
                        .flatMap(p => p.parcelas)
                        .filter(pa => pa.pago_em && pa.pago_em.startsWith(mesAtual))
                        .reduce((s, pa) => s + pa.valor, 0);
                    this.metricas.ativas = this.pendencias.filter(p => p.status !== 'pago').length;
                },

                // ── Ações — nova pendência ───────────────────────────────────

                abrirModalNova() {
                    this.nf = {
                        tipo: 'os', os_selecionada: null, os_busca: '',
                        ja_pago: '', cliente_nome: '', valor_avulso: '',
                        descricao: '', num_parcelas: 1, parcelas: [],
                    };
                    this.etapa    = 1;
                    this.modalNova = true;
                },

                selecionarOs(os) {
                    this.nf.os_selecionada = os;
                    this.nf.os_busca       = os.id;
                    this.nf.descricao      = 'Saldo restante ' + os.id;
                },

                avancarParaEtapa2() {
                    if (!this.etapa1Valida) return;
                    this.gerarParcelas();
                    this.etapa = 2;
                },

                gerarParcelas() {
                    const valor = this.valorAParcerlar;
                    const n     = parseInt(this.nf.num_parcelas) || 1;
                    if (valor <= 0 || n < 1) { this.nf.parcelas = []; return; }

                    const base  = Math.floor(valor * 100 / n) / 100;
                    const resto = Math.round((valor - base * n) * 100) / 100;

                    this.nf.parcelas = Array.from({ length: n }, (_, i) => {
                        const d = new Date();
                        d.setDate(d.getDate() + 30 * (i + 1));
                        return {
                            numero:     i + 1,
                            valor:      i === n - 1 ? Math.round((base + resto) * 100) / 100 : base,
                            vencimento: d.toISOString().split('T')[0],
                        };
                    });
                },

                criarPendencia() {
                    if (this.nf.parcelas.length === 0 || this.valorAParcerlar <= 0) return;

                    const id      = 'PEND-' + Date.now();
                    const cliente = this.nf.tipo === 'os'
                        ? (this.nf.os_selecionada?.cliente || '')
                        : this.nf.cliente_nome.trim();
                    const descricao = this.nf.descricao.trim() ||
                        (this.nf.tipo === 'os' ? 'Saldo restante ' + this.nf.os_selecionada?.id : '');

                    this.pendencias.push({
                        id,
                        tipo:         this.nf.tipo,
                        os_id:        this.nf.tipo === 'os' ? (this.nf.os_selecionada?.id || null) : null,
                        cliente_id:   null,
                        cliente,
                        descricao,
                        valor_total:  this.valorAParcerlar,
                        valor_pago:   0,
                        status:       this.calcularStatus(this.nf.parcelas.map(p => ({ ...p, pago_em: null }))),
                        data_criacao: new Date().toISOString().split('T')[0],
                        parcelas:     this.nf.parcelas.map(p => ({ ...p, pago_em: null, forma_pagamento: null })),
                    });

                    this.recalcularMetricas();
                    this.fecharModais();
                    this.mostrarToast('Pendência criada com sucesso!');
                },

                // ── Utilitários ──────────────────────────────────────────────

                fecharModais() {
                    this.mpAberto   = false;
                    this.modalNova  = false;
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
