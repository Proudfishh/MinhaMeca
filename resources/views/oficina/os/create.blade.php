<x-layouts.oficina title="Nova OS">

{{-- ===== HEADER ===== --}}
<div class="flex items-center gap-2 mb-6">
    <a href="{{ route('oficina.os.index') }}"
       class="flex items-center gap-1.5 text-sm text-muted hover:text-void transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Ordens de Serviço
    </a>
    <span class="text-muted/40 text-sm">·</span>
    <span class="font-display font-semibold text-void text-sm">Nova OS</span>
</div>

{{-- ===== WIZARD ===== --}}
<div x-data="novaOs()" class="max-w-2xl mx-auto">

    <form action="{{ route('oficina.os.store') }}" method="POST" id="form-nova-os">
        @csrf

        {{-- Hidden inputs sincronizados pelo Alpine --}}
        <input type="hidden" name="cliente_modo"         :value="clienteMode">
        <input type="hidden" name="cliente_id"           :value="clienteSelecionado?.id ?? ''">
        <input type="hidden" name="cliente_nome_novo"    :value="novoCliente.nome">
        <input type="hidden" name="cliente_tipo"         :value="novoCliente.tipo">
        <input type="hidden" name="cliente_cpf_novo"     :value="novoCliente.cpf">
        <input type="hidden" name="cliente_cnpj_novo"    :value="novoCliente.cnpj">
        <input type="hidden" name="cliente_telefone_novo":value="novoCliente.telefone">
        <input type="hidden" name="cliente_email_novo"   :value="novoCliente.email">
        <input type="hidden" name="veiculo_modo"         :value="veiculoMode">
        <input type="hidden" name="veiculo_id"           :value="veiculoSelecionado?.id ?? ''">
        <input type="hidden" name="veiculo_placa_novo"   :value="novoVeiculo.placa">
        <input type="hidden" name="veiculo_marca_novo"   :value="novoVeiculo.marca">
        <input type="hidden" name="veiculo_modelo_novo"  :value="novoVeiculo.modelo">
        <input type="hidden" name="veiculo_ano_novo"     :value="novoVeiculo.ano">
        <input type="hidden" name="veiculo_cor_novo"     :value="novoVeiculo.cor">
        <input type="hidden" name="descricao"            :value="descricao">
        <input type="hidden" name="mecanico"             :value="mecanico">

        {{-- ---- Stepper Desktop ---- --}}
        <div class="hidden sm:flex items-center mb-6 px-1">
            <template x-for="(label, i) in stepLabels" :key="i">
                <div class="flex items-center" :class="i < stepLabels.length - 1 ? 'flex-1' : ''">
                    <div class="flex flex-col items-center gap-1.5">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-200"
                             :class="{
                                 'bg-spark text-white': step > i + 1,
                                 'ring-2 ring-spark text-spark bg-white': step === i + 1,
                                 'ring-2 ring-border text-muted bg-white': step < i + 1,
                             }">
                            <template x-if="step > i + 1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <template x-if="step <= i + 1">
                                <span x-text="i + 1"></span>
                            </template>
                        </div>
                        <span class="text-xs font-medium transition-colors"
                              :class="{
                                  'text-spark': step === i + 1,
                                  'text-void': step > i + 1,
                                  'text-muted': step < i + 1,
                              }"
                              x-text="label"></span>
                    </div>
                    <div x-show="i < stepLabels.length - 1"
                         class="flex-1 h-px mx-3 mb-5 transition-colors duration-200"
                         :class="step > i + 1 ? 'bg-spark' : 'bg-border'"></div>
                </div>
            </template>
        </div>

        {{-- ---- Stepper Mobile ---- --}}
        <div class="sm:hidden mb-5">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-muted">
                    Etapa <span x-text="step"></span> de <span x-text="stepLabels.length"></span>
                </p>
                <p class="text-xs font-semibold text-void font-display" x-text="stepLabels[step - 1]"></p>
            </div>
            <div class="h-1 bg-border rounded-full overflow-hidden">
                <div class="h-full bg-spark rounded-full transition-all duration-300"
                     :style="`width: ${(step / stepLabels.length) * 100}%`"></div>
            </div>
        </div>

        {{-- ---- Card ---- --}}
        <div class="bg-white rounded-xl" style="border: 1px solid var(--color-border);">

            {{-- ===== ETAPA 1: CLIENTE ===== --}}
            <div x-show="step === 1" class="p-5 sm:p-6">
                <h2 class="font-display font-semibold text-void text-lg mb-0.5">Cliente</h2>
                <p class="text-sm text-muted mb-5">Vincule um cliente existente, cadastre um novo ou abra sem cliente.</p>

                {{-- Pill selectors --}}
                <div class="flex flex-wrap gap-2 mb-6">
                    <button type="button" @click="setClienteMode('existente')"
                            class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                            :class="clienteMode === 'existente'
                                ? 'bg-spark text-white border-spark'
                                : 'bg-white text-muted border-border hover:border-spark/40 hover:text-void'">
                        Já cadastrado
                    </button>
                    <button type="button" @click="setClienteMode('novo')"
                            class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                            :class="clienteMode === 'novo'
                                ? 'bg-spark text-white border-spark'
                                : 'bg-white text-muted border-border hover:border-spark/40 hover:text-void'">
                        Novo cliente
                    </button>
                    <button type="button" @click="setClienteMode('sem')"
                            class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                            :class="clienteMode === 'sem'
                                ? 'bg-spark text-white border-spark'
                                : 'bg-white text-muted border-border hover:border-spark/40 hover:text-void'">
                        Sem cliente
                    </button>
                </div>

                {{-- Já cadastrado --}}
                <div x-show="clienteMode === 'existente'"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="relative mb-3">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="clienteBusca"
                               placeholder="Buscar por nome, CPF ou telefone..."
                               class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors"
                               style="--tw-ring-color: #3B82F620;">
                    </div>
                    <div class="space-y-2 max-h-56 overflow-y-auto">
                        <template x-for="c in clientesFiltrados" :key="c.id">
                            <div class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-all select-none"
                                 :class="clienteSelecionado?.id === c.id
                                     ? 'border-spark bg-spark/5'
                                     : 'border-border hover:border-spark/30 hover:bg-surface'"
                                 @click="selecionarCliente(c)">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background:#1E3A5F;">
                                    <span class="text-white text-xs font-bold uppercase" x-text="c.nome.charAt(0)"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-void truncate" x-text="c.nome"></p>
                                    <p class="text-xs text-muted" x-text="c.cpf + ' · ' + c.telefone"></p>
                                </div>
                                <svg x-show="clienteSelecionado?.id === c.id"
                                     class="w-4 h-4 text-spark flex-shrink-0"
                                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </template>
                        <p x-show="clientesFiltrados.length === 0"
                           class="text-center text-sm text-muted py-8">
                            Nenhum cliente encontrado.
                        </p>
                    </div>
                </div>

                {{-- Novo cliente --}}
                <div x-show="clienteMode === 'novo'"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0">

                    {{-- Toggle PF / PJ --}}
                    <div class="flex gap-2 mb-4">
                        <button type="button" @click="novoCliente.tipo = 'pf'"
                                class="px-3 py-1.5 rounded-full text-xs font-medium border transition-all"
                                :class="novoCliente.tipo === 'pf'
                                    ? 'bg-spark text-white border-spark'
                                    : 'bg-white text-muted border-border hover:border-spark/40 hover:text-void'">
                            PF — Pessoa Física
                        </button>
                        <button type="button" @click="novoCliente.tipo = 'pj'"
                                class="px-3 py-1.5 rounded-full text-xs font-medium border transition-all"
                                :class="novoCliente.tipo === 'pj'
                                    ? 'bg-spark text-white border-spark'
                                    : 'bg-white text-muted border-border hover:border-spark/40 hover:text-void'">
                            PJ — Pessoa Jurídica
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- PF: Nome completo --}}
                        <div class="sm:col-span-2" x-show="novoCliente.tipo === 'pf'">
                            <label class="block text-xs font-medium text-muted mb-1.5">Nome completo <span class="text-spark">*</span></label>
                            <input type="text" x-model="novoCliente.nome" placeholder="Ex: João da Silva"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>

                        {{-- PJ: Razão Social --}}
                        <div class="sm:col-span-2" x-show="novoCliente.tipo === 'pj'">
                            <label class="block text-xs font-medium text-muted mb-1.5">Razão Social <span class="text-spark">*</span></label>
                            <input type="text" x-model="novoCliente.nome" placeholder="Ex: Auto Peças Ltda"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>

                        {{-- PF: CPF --}}
                        <div x-show="novoCliente.tipo === 'pf'">
                            <label class="block text-xs font-medium text-muted mb-1.5">CPF <span class="text-spark">*</span></label>
                            <input type="text" x-model="novoCliente.cpf" placeholder="000.000.000-00"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>

                        {{-- PJ: CNPJ --}}
                        <div x-show="novoCliente.tipo === 'pj'">
                            <label class="block text-xs font-medium text-muted mb-1.5">CNPJ <span class="text-spark">*</span></label>
                            <input type="text" x-model="novoCliente.cnpj" placeholder="00.000.000/0000-00"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>

                        {{-- Telefone (comum) --}}
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Telefone <span class="text-spark">*</span></label>
                            <input type="text" x-model="novoCliente.telefone" placeholder="(00) 00000-0000"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>

                        {{-- E-mail (apenas PF) --}}
                        <div class="sm:col-span-2" x-show="novoCliente.tipo === 'pf'">
                            <label class="block text-xs font-medium text-muted mb-1.5">E-mail</label>
                            <input type="email" x-model="novoCliente.email" placeholder="email@exemplo.com"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>

                    </div>
                </div>

                {{-- Sem cliente --}}
                <div x-show="clienteMode === 'sem'"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="flex items-start gap-3 p-4 rounded-lg bg-surface" style="border: 1px solid var(--color-border);">
                        <svg class="w-4 h-4 text-muted flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-muted leading-relaxed">
                            A OS será aberta sem cliente vinculado. Você pode vincular um cliente posteriormente na tela de detalhe.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ===== ETAPA 2: VEÍCULO ===== --}}
            <div x-show="step === 2" class="p-5 sm:p-6">
                <h2 class="font-display font-semibold text-void text-lg mb-0.5">Veículo</h2>
                <p class="text-sm text-muted mb-5">Selecione o veículo do cliente, cadastre um novo ou continue sem veículo.</p>

                <div class="flex flex-wrap gap-2 mb-6">
                    <button type="button" @click="setVeiculoMode('existente')"
                            class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                            :class="veiculoMode === 'existente'
                                ? 'bg-spark text-white border-spark'
                                : 'bg-white text-muted border-border hover:border-spark/40 hover:text-void'">
                        Veículo existente
                    </button>
                    <button type="button" @click="setVeiculoMode('novo')"
                            class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                            :class="veiculoMode === 'novo'
                                ? 'bg-spark text-white border-spark'
                                : 'bg-white text-muted border-border hover:border-spark/40 hover:text-void'">
                        Novo veículo
                    </button>
                    <button type="button" @click="setVeiculoMode('sem')"
                            class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                            :class="veiculoMode === 'sem'
                                ? 'bg-spark text-white border-spark'
                                : 'bg-white text-muted border-border hover:border-spark/40 hover:text-void'">
                        Sem veículo
                    </button>
                </div>

                {{-- Veículo existente --}}
                <div x-show="veiculoMode === 'existente'"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div x-show="!clienteSelecionado" class="relative mb-3">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="veiculoBusca"
                               placeholder="Buscar por placa, marca ou modelo..."
                               class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                    </div>
                    <p x-show="clienteSelecionado" class="text-xs font-medium text-muted mb-3">
                        Veículos de <span x-text="clienteSelecionado?.nome" class="text-void font-semibold"></span>
                    </p>
                    <div class="space-y-2 max-h-56 overflow-y-auto">
                        <template x-for="v in veiculosFiltrados" :key="v.id">
                            <div class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-all select-none"
                                 :class="veiculoSelecionado?.id === v.id
                                     ? 'border-spark bg-spark/5'
                                     : 'border-border hover:border-spark/30 hover:bg-surface'"
                                 @click="selecionarVeiculo(v)">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                     style="background:#0F172A0D; border:1px solid #0F172A18;">
                                    <svg class="w-5 h-5" fill="none" stroke="#64748B" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-void font-mono" x-text="v.placa"></p>
                                    <p class="text-xs text-muted" x-text="`${v.marca} ${v.modelo} ${v.ano} · ${v.cor}`"></p>
                                </div>
                                <svg x-show="veiculoSelecionado?.id === v.id"
                                     class="w-4 h-4 text-spark flex-shrink-0"
                                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </template>
                        <p x-show="veiculosFiltrados.length === 0"
                           class="text-center text-sm text-muted py-8">
                            Nenhum veículo encontrado.
                        </p>
                    </div>
                </div>

                {{-- Novo veículo --}}
                <div x-show="veiculoMode === 'novo'"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Placa <span class="text-spark">*</span></label>
                            <input type="text" x-model="novoVeiculo.placa" placeholder="ABC-1234"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm font-mono text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors"
                                   style="text-transform:uppercase;">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Marca <span class="text-spark">*</span></label>
                            <input type="text" x-model="novoVeiculo.marca" placeholder="Ex: Honda"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Modelo <span class="text-spark">*</span></label>
                            <input type="text" x-model="novoVeiculo.modelo" placeholder="Ex: Civic"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Ano <span class="text-spark">*</span></label>
                            <input type="number" x-model="novoVeiculo.ano" placeholder="2024" min="1960" max="2030"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-muted mb-1.5">Cor</label>
                            <input type="text" x-model="novoVeiculo.cor" placeholder="Ex: Prata"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>
                    </div>
                </div>

                {{-- Sem veículo --}}
                <div x-show="veiculoMode === 'sem'"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="flex items-start gap-3 p-4 rounded-lg bg-surface" style="border: 1px solid var(--color-border);">
                        <svg class="w-4 h-4 text-muted flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-muted leading-relaxed">
                            A OS será aberta sem veículo vinculado. Você pode vincular um veículo posteriormente na tela de detalhe.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ===== ETAPA 3: PROBLEMA ===== --}}
            <div x-show="step === 3" class="p-5 sm:p-6">
                <h2 class="font-display font-semibold text-void text-lg mb-0.5">Problema</h2>
                <p class="text-sm text-muted mb-5">Descreva o que o cliente relatou e atribua um mecânico responsável.</p>

                {{-- Mini-resumo --}}
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-surface text-void"
                          style="border: 1px solid var(--color-border);">
                        <svg class="w-3.5 h-3.5 text-muted flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span x-text="resumoCliente"></span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-surface text-void"
                          style="border: 1px solid var(--color-border);">
                        <svg class="w-3.5 h-3.5 text-muted flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z"/>
                        </svg>
                        <span x-text="resumoVeiculo"></span>
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">
                            O que o cliente relatou <span class="text-spark">*</span>
                        </label>
                        <textarea x-model="descricao" rows="5"
                                  placeholder="Ex: Cliente relata barulho ao frear no lado dianteiro esquerdo ao reduzir velocidade acima de 80 km/h."
                                  class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors resize-none leading-relaxed"></textarea>
                        <p class="text-xs mt-1 transition-colors"
                           :class="descricao.length > 0 && descricao.trim().length < 10 ? 'text-amber-500' : 'text-muted'">
                            <span x-text="descricao.trim().length"></span> caracteres
                            <span x-show="descricao.length > 0 && descricao.trim().length < 10"> · mínimo 10</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Mecânico responsável</label>
                        <div class="relative">
                            <select x-model="mecanico"
                                    class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors appearance-none cursor-pointer pr-8">
                                <option value="">Atribuir depois</option>
                                <option value="Marcos Ferreira">Marcos Ferreira</option>
                                <option value="João Oliveira">João Oliveira</option>
                                <option value="Paulo Santos">Paulo Santos</option>
                            </select>
                            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ---- Nav Desktop (dentro do card) ---- --}}
            <div class="hidden sm:flex items-center justify-between px-5 py-4"
                 style="border-top: 1px solid var(--color-border); background: #F8FAFC80;">
                <button type="button" @click="voltar()"
                        x-show="step > 1"
                        class="flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-muted hover:text-void border border-border rounded-lg hover:border-void/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Voltar
                </button>
                <span x-show="step === 1"></span>

                <button type="button" @click="avancar()"
                        x-show="step < 3"
                        :disabled="!podeAvancar"
                        class="flex items-center gap-1.5 px-5 py-2 text-sm font-semibold rounded-lg transition-all"
                        :class="podeAvancar
                            ? 'bg-spark text-white hover:bg-spark/90'
                            : 'bg-border text-muted cursor-not-allowed'">
                    Avançar
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <button type="submit"
                        x-show="step === 3"
                        :disabled="!podeAvancar"
                        class="flex items-center gap-1.5 px-5 py-2 text-sm font-semibold rounded-lg transition-all"
                        :class="podeAvancar
                            ? 'bg-spark text-white hover:bg-spark/90'
                            : 'bg-border text-muted cursor-not-allowed'">
                    Abrir OS
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </div>

        </div>{{-- /card --}}

        {{-- ---- Nav Mobile (fixed bottom) ---- --}}
        <div class="sm:hidden fixed bottom-14 left-0 right-0 z-50 px-4 py-3 bg-white flex items-center gap-3"
             style="border-top: 1px solid var(--color-border); box-shadow: 0 -4px 20px rgba(15,23,42,0.07);">
            <button type="button" @click="voltar()"
                    x-show="step > 1"
                    class="flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-medium text-muted border border-border rounded-lg hover:border-void/20 hover:text-void transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar
            </button>

            <button type="button" @click="avancar()"
                    x-show="step < 3"
                    :disabled="!podeAvancar"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-sm font-semibold rounded-lg transition-all"
                    :class="podeAvancar
                        ? 'bg-spark text-white'
                        : 'bg-border text-muted cursor-not-allowed'">
                Avançar
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <button type="submit"
                    x-show="step === 3"
                    :disabled="!podeAvancar"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-sm font-semibold rounded-lg transition-all"
                    :class="podeAvancar
                        ? 'bg-spark text-white'
                        : 'bg-border text-muted cursor-not-allowed'">
                Abrir OS
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </button>
        </div>

        {{-- Espaço para o nav mobile não sobrepor o card --}}
        <div class="sm:hidden h-36"></div>

    </form>
</div>{{-- /wizard --}}

<script>
function novaOs() {
    return {
        step: 1,
        stepLabels: ['Cliente', 'Veículo', 'Problema'],
        clientes: {!! json_encode($clientes, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!},
        veiculos: {!! json_encode($veiculos, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!},

        clienteMode: '',
        clienteBusca: '',
        clienteSelecionado: null,
        novoCliente: { tipo: 'pf', nome: '', cpf: '', cnpj: '', telefone: '', email: '' },

        veiculoMode: '',
        veiculoBusca: '',
        veiculoSelecionado: null,
        novoVeiculo: { placa: '', marca: '', modelo: '', ano: '', cor: '' },

        descricao: '',
        mecanico: '',

        setClienteMode(mode) {
            this.clienteMode = mode;
            this.clienteSelecionado = null;
            this.clienteBusca = '';
        },

        setVeiculoMode(mode) {
            this.veiculoMode = mode;
            this.veiculoSelecionado = null;
            this.veiculoBusca = '';
        },

        selecionarCliente(c) {
            this.clienteSelecionado = this.clienteSelecionado?.id === c.id ? null : c;
        },

        selecionarVeiculo(v) {
            this.veiculoSelecionado = this.veiculoSelecionado?.id === v.id ? null : v;
        },

        get clientesFiltrados() {
            if (!this.clienteBusca.trim()) return this.clientes;
            const q = this.clienteBusca.toLowerCase();
            return this.clientes.filter(c =>
                c.nome.toLowerCase().includes(q) ||
                c.cpf.includes(q) ||
                c.telefone.includes(q)
            );
        },

        get veiculosFiltrados() {
            if (this.clienteSelecionado) {
                return this.veiculos.filter(v => v.cliente_id === this.clienteSelecionado.id);
            }
            if (!this.veiculoBusca.trim()) return this.veiculos;
            const q = this.veiculoBusca.toLowerCase();
            return this.veiculos.filter(v =>
                v.placa.toLowerCase().includes(q) ||
                v.modelo.toLowerCase().includes(q) ||
                v.marca.toLowerCase().includes(q)
            );
        },

        get resumoCliente() {
            if (this.clienteMode === 'sem') return 'Sem cliente';
            if (this.clienteMode === 'existente' && this.clienteSelecionado) return this.clienteSelecionado.nome;
            if (this.clienteMode === 'novo' && this.novoCliente.nome) return this.novoCliente.nome;
            return 'Não informado';
        },

        get resumoVeiculo() {
            if (this.veiculoMode === 'sem') return 'Sem veículo';
            if (this.veiculoMode === 'existente' && this.veiculoSelecionado) {
                return `${this.veiculoSelecionado.placa} · ${this.veiculoSelecionado.marca} ${this.veiculoSelecionado.modelo}`;
            }
            if (this.veiculoMode === 'novo' && this.novoVeiculo.placa) {
                const desc = this.novoVeiculo.modelo
                    ? ` · ${this.novoVeiculo.marca} ${this.novoVeiculo.modelo}`
                    : '';
                return `${this.novoVeiculo.placa}${desc}`;
            }
            return 'Não informado';
        },

        get podeAvancar() {
            if (this.step === 1) {
                if (this.clienteMode === '') return false;
                if (this.clienteMode === 'sem') return true;
                if (this.clienteMode === 'existente') return this.clienteSelecionado !== null;
                if (this.clienteMode === 'novo') {
                    const doc = this.novoCliente.tipo === 'pj'
                        ? this.novoCliente.cnpj.trim() !== ''
                        : this.novoCliente.cpf.trim() !== '';
                    return this.novoCliente.nome.trim() !== '' &&
                           doc &&
                           this.novoCliente.telefone.trim() !== '';
                }
            }
            if (this.step === 2) {
                if (this.veiculoMode === '') return false;
                if (this.veiculoMode === 'sem') return true;
                if (this.veiculoMode === 'existente') return this.veiculoSelecionado !== null;
                if (this.veiculoMode === 'novo') {
                    return this.novoVeiculo.placa.trim() !== '' &&
                           this.novoVeiculo.marca.trim() !== '' &&
                           this.novoVeiculo.modelo.trim() !== '';
                }
            }
            if (this.step === 3) {
                return this.descricao.trim().length >= 10;
            }
            return false;
        },

        avancar() {
            if (this.podeAvancar && this.step < 3) this.step++;
        },

        voltar() {
            if (this.step > 1) this.step--;
        },
    };
}
</script>

</x-layouts.oficina>
