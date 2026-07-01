<x-layouts.oficina title="Clientes">

    <script>
        window.__clientes = {!! json_encode($clientes, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    </script>

    <div x-data="clientesIndex()" x-init="init()" @keydown.escape.window="fecharModal()">

        {{-- ==================== HEADER ==================== --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <h2 class="font-display font-bold text-void text-xl">Clientes</h2>
                <span class="font-mono text-xs px-2 py-0.5 rounded-full bg-ocean/10 text-ocean font-semibold"
                      x-text="clientes.length"></span>
            </div>
            <button type="button"
                    @click="abrirModal()"
                    class="hidden md:inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-spark text-white text-sm font-medium hover:bg-spark/90 transition-colors">
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
                        @click="abrirModal()"
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
                                        <template x-if="cliente.os_ativa">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold text-white"
                                                  :style="'background:' + etapasCor[cliente.os_ativa.etapa_atual]"
                                                  x-text="etapasLabel[cliente.os_ativa.etapa_atual]">
                                            </span>
                                        </template>
                                        <span class="text-[11px] text-muted"
                                              x-text="cliente.total_os + ' OS'">
                                        </span>
                                    </div>
                                </div>

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

        {{-- FAB mobile --}}
        <button type="button" @click="abrirModal()"
                class="md:hidden fixed right-4 z-30 w-14 h-14 rounded-full text-white flex items-center justify-center shadow-lg"
                style="bottom:5rem;background:var(--color-spark);box-shadow:0 4px 16px rgba(59,130,246,0.45);"
                aria-label="Novo cliente">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
        </button>

        {{-- ==================== MODAL NOVO CLIENTE ==================== --}}
        <template x-teleport="body">
            <div x-show="modalAberto"
                 class="fixed inset-0 z-40 flex items-center justify-center p-4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">

                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-void/50" @click="fecharModal()"></div>

                {{-- Painel --}}
                <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     @click.stop>

                    {{-- Header do modal --}}
                    <div class="flex items-center justify-between px-6 py-4"
                         style="border-bottom: 1px solid var(--color-border);">
                        <h3 class="font-display font-semibold text-void text-base">Novo Cliente</h3>
                        <button type="button" @click="fecharModal()"
                                class="text-muted hover:text-void transition-colors rounded-lg p-1 hover:bg-surface">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Body do modal --}}
                    <div class="px-6 py-5 space-y-4">

                        {{-- Toggle PF / PJ --}}
                        <div class="flex gap-2">
                            <button type="button" @click="form.tipo = 'pf'"
                                    class="px-3 py-1.5 rounded-full text-xs font-medium border transition-all"
                                    :class="form.tipo === 'pf'
                                        ? 'bg-spark text-white border-spark'
                                        : 'bg-white text-muted border-border hover:border-spark/40 hover:text-void'">
                                PF — Pessoa Física
                            </button>
                            <button type="button" @click="form.tipo = 'pj'"
                                    class="px-3 py-1.5 rounded-full text-xs font-medium border transition-all"
                                    :class="form.tipo === 'pj'
                                        ? 'bg-spark text-white border-spark'
                                        : 'bg-white text-muted border-border hover:border-spark/40 hover:text-void'">
                                PJ — Pessoa Jurídica
                            </button>
                        </div>

                        {{-- Nome / Razão Social --}}
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">
                                <span x-text="form.tipo === 'pj' ? 'Razão Social' : 'Nome completo'"></span>
                                <span class="text-spark">*</span>
                            </label>
                            <input type="text" x-model="form.nome"
                                   :placeholder="form.tipo === 'pj' ? 'Ex: Auto Peças Ltda' : 'Ex: João da Silva'"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>

                        {{-- CPF ou CNPJ --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div x-show="form.tipo === 'pf'">
                                <label class="block text-xs font-medium text-muted mb-1.5">CPF <span class="text-spark">*</span></label>
                                <input type="text" x-model="form.cpf" placeholder="000.000.000-00"
                                       class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                            </div>
                            <div x-show="form.tipo === 'pj'">
                                <label class="block text-xs font-medium text-muted mb-1.5">CNPJ <span class="text-spark">*</span></label>
                                <input type="text" x-model="form.cnpj" placeholder="00.000.000/0000-00"
                                       class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-muted mb-1.5">Telefone <span class="text-spark">*</span></label>
                                <input type="text" x-model="form.telefone" placeholder="(00) 00000-0000"
                                       class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                            </div>
                        </div>

                        {{-- E-mail --}}
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">E-mail</label>
                            <input type="email" x-model="form.email" placeholder="email@exemplo.com"
                                   class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                        </div>

                        {{-- Toggle Informações adicionais --}}
                        <div class="pt-1" style="border-top: 1px solid var(--color-border);">
                            <button type="button" @click="mostrarAdicionais = !mostrarAdicionais"
                                    class="flex items-center gap-1.5 text-xs font-medium text-muted hover:text-void transition-colors py-1">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                     :class="mostrarAdicionais ? 'rotate-90' : ''"
                                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                                Informações adicionais
                                <span class="text-[10px] font-normal text-muted/70">(opcional)</span>
                            </button>
                        </div>

                        {{-- Campos adicionais --}}
                        <div x-show="mostrarAdicionais"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-3">

                            {{-- Nome do contato — só PJ --}}
                            <div x-show="form.tipo === 'pj'">
                                <label class="block text-xs font-medium text-muted mb-1.5">Nome do contato responsável</label>
                                <input type="text" x-model="form.nome_contato" placeholder="Ex: João Silva"
                                       class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                            </div>

                            {{-- Endereço --}}
                            <div class="grid grid-cols-3 gap-3">
                                <div class="col-span-1">
                                    <label class="block text-xs font-medium text-muted mb-1.5">CEP</label>
                                    <input type="text" x-model="form.cep" placeholder="00000-000"
                                           class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-muted mb-1.5">Logradouro</label>
                                    <input type="text" x-model="form.logradouro" placeholder="Rua, Av., Travessa..."
                                           class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-muted mb-1.5">Número</label>
                                    <input type="text" x-model="form.numero" placeholder="123"
                                           class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-muted mb-1.5">Complemento</label>
                                    <input type="text" x-model="form.complemento" placeholder="Apto, Sala, Bloco..."
                                           class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                </div>
                            </div>
                            <div class="grid grid-cols-5 gap-3">
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-muted mb-1.5">Bairro</label>
                                    <input type="text" x-model="form.bairro" placeholder="Bairro"
                                           class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-muted mb-1.5">Cidade</label>
                                    <input type="text" x-model="form.cidade" placeholder="Cidade"
                                           class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-muted mb-1.5">UF</label>
                                    <input type="text" x-model="form.uf" placeholder="SP" maxlength="2"
                                           class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors uppercase">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Footer do modal --}}
                    <div class="flex items-center justify-end gap-3 px-6 py-4"
                         style="border-top: 1px solid var(--color-border);">
                        <button type="button" @click="fecharModal()"
                                class="px-4 py-2 rounded-lg border border-border text-void text-sm font-medium hover:bg-surface transition-colors">
                            Cancelar
                        </button>
                        <button type="button" @click="salvarCliente()"
                                :disabled="!formValido"
                                :class="formValido
                                    ? 'bg-spark text-white hover:bg-spark/90 cursor-pointer'
                                    : 'bg-border text-muted cursor-not-allowed'"
                                class="px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                            Cadastrar
                        </button>
                    </div>

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

                modalAberto: false,
                mostrarAdicionais: false,
                toastVisible: false,
                toastMsg: '',
                _toastTimer: null,

                form: {
                    tipo: 'pf',
                    nome: '',
                    cpf: '',
                    cnpj: '',
                    telefone: '',
                    email: '',
                    nome_contato: '',
                    cep: '',
                    logradouro: '',
                    numero: '',
                    complemento: '',
                    bairro: '',
                    cidade: '',
                    uf: '',
                },

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
                        (c.cpf  && c.cpf.includes(q))   ||
                        (c.cnpj && c.cnpj.includes(q))  ||
                        c.telefone.includes(q)
                    );
                },

                get formValido() {
                    if (!this.form.nome.trim() || !this.form.telefone.trim()) return false;
                    if (this.form.tipo === 'pf') return this.form.cpf.trim() !== '';
                    return this.form.cnpj.trim() !== '';
                },

                abrirModal() {
                    this.form = {
                        tipo: 'pf', nome: '', cpf: '', cnpj: '', telefone: '', email: '',
                        nome_contato: '', cep: '', logradouro: '', numero: '',
                        complemento: '', bairro: '', cidade: '', uf: '',
                    };
                    this.mostrarAdicionais = false;
                    this.modalAberto = true;
                },

                fecharModal() {
                    this.modalAberto = false;
                },

                salvarCliente() {
                    if (!this.formValido) return;

                    const novoId = Date.now();
                    this.clientes.push({
                        id:             novoId,
                        tipo:           this.form.tipo,
                        nome:           this.form.nome.trim(),
                        cpf:            this.form.tipo === 'pf' ? this.form.cpf.trim() : null,
                        cnpj:           this.form.tipo === 'pj' ? this.form.cnpj.trim() : null,
                        nome_contato:   this.form.nome_contato.trim() || null,
                        telefone:       this.form.telefone.trim(),
                        email:          this.form.email.trim() || null,
                        cep:            this.form.cep.trim() || null,
                        logradouro:     this.form.logradouro.trim() || null,
                        numero:         this.form.numero.trim() || null,
                        complemento:    this.form.complemento.trim() || null,
                        bairro:         this.form.bairro.trim() || null,
                        cidade:         this.form.cidade.trim() || null,
                        uf:             this.form.uf.trim().toUpperCase() || null,
                        total_os:       0,
                        os_ativa:       null,
                        total_veiculos: 0,
                    });

                    this.fecharModal();
                    this.mostrarToast('Cliente cadastrado com sucesso!');
                },

                mostrarToast(msg) {
                    this.toastMsg = msg;
                    this.toastVisible = true;
                    clearTimeout(this._toastTimer);
                    this._toastTimer = setTimeout(() => { this.toastVisible = false; }, 3000);
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
