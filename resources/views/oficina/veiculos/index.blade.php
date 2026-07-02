<x-layouts.oficina title="Veículos">

    <script>
        window.__veiculos = {!! json_encode($veiculos, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__etapas   = {!! json_encode($etapas,   JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        window.__clientes = {!! json_encode($clientes, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    </script>

    <div x-data="veiculosPage()" x-init="init()" @keydown.escape.window="fecharModal()">

        {{-- ==================== HEADER ==================== --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <h2 class="font-display font-bold text-void text-xl">Veículos</h2>
                <span class="font-mono text-xs px-2 py-0.5 rounded-full bg-ocean/10 text-ocean font-semibold"
                      x-text="veiculos.length"></span>
            </div>
            <button type="button" @click="abrirModal()"
                    class="hidden md:inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-spark text-white text-sm font-medium hover:bg-spark/90 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Veículo
            </button>
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

        @if(session('sucesso'))
            <div class="flex items-center gap-2 px-4 py-3 rounded-xl mb-4 text-sm font-medium"
                 style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);color:#059669;">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('sucesso') }}
            </div>
        @endif

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
                                <p class="text-muted text-xs truncate" x-text="v.ano + ' · ' + v.cor + ' · ' + v.cliente_nome"></p>
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
                                    <span class="text-void text-sm" x-text="v.cliente_nome"></span>
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

        {{-- FAB mobile --}}
        <button type="button" @click="abrirModal()"
                class="md:hidden fixed right-4 z-30 w-14 h-14 rounded-full text-white flex items-center justify-center shadow-lg"
                style="bottom:5rem;background:var(--color-spark);box-shadow:0 4px 16px rgba(59,130,246,0.45);"
                aria-label="Novo veículo">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
        </button>

        {{-- ==================== MODAL NOVO VEÍCULO ==================== --}}
        <template x-teleport="body">
            <div x-show="modalAberto"
                 class="fixed inset-0 z-40 flex items-center justify-center p-4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-cloak>

                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-void/50" @click="fecharModal()"></div>

                {{-- Painel --}}
                <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     @click.stop>

                    {{-- Header do modal --}}
                    <div class="flex items-center justify-between px-6 py-4 flex-shrink-0"
                         style="border-bottom: 1px solid var(--color-border);">
                        <h3 class="font-display font-semibold text-void text-base">Novo Veículo</h3>
                        <button type="button" @click="fecharModal()"
                                class="text-muted hover:text-void transition-colors rounded-lg p-1 hover:bg-surface">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Body do modal --}}
                    <form method="POST" action="{{ route('oficina.veiculos.store') }}" class="px-6 py-5 space-y-4 overflow-y-auto">
                        @csrf

                        @if ($errors->any())
                            <div class="rounded-lg px-3 py-2 text-xs text-red-600"
                                 style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);">
                                <ul class="list-disc pl-4 space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Marca / Modelo --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-muted mb-1.5">Marca <span class="text-spark">*</span></label>
                                <input type="text" name="marca" x-model="form.marca" required placeholder="Ex: Honda"
                                       class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-muted mb-1.5">Modelo <span class="text-spark">*</span></label>
                                <input type="text" name="modelo" x-model="form.modelo" required placeholder="Ex: Civic"
                                       class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                            </div>
                        </div>

                        {{-- Placa / Ano / Cor --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-muted mb-1.5">Placa <span class="text-spark">*</span></label>
                                <input type="text" name="placa" x-model="form.placa" required placeholder="ABC-1234"
                                       class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors uppercase">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-muted mb-1.5">Ano</label>
                                <input type="text" name="ano" inputmode="numeric" x-model="form.ano" placeholder="2020" maxlength="4"
                                       class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-muted mb-1.5">Cor</label>
                                <input type="text" name="cor" x-model="form.cor" placeholder="Prata"
                                       class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                            </div>
                        </div>

                        {{-- Cliente (opcional) --}}
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">
                                Vincular a um cliente <span class="text-[10px] font-normal text-muted/70">(opcional — pode vincular depois)</span>
                            </label>
                            <select name="cliente_id" x-model="form.cliente_id"
                                    class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                <option value="">Sem cliente</option>
                                <template x-for="c in clientes" :key="c.id">
                                    <option :value="c.id" x-text="c.nome"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Toggle Mais detalhes --}}
                        <div class="pt-1" style="border-top: 1px solid var(--color-border);">
                            <button type="button" @click="mostrarMais = !mostrarMais"
                                    class="flex items-center gap-1.5 text-xs font-medium text-muted hover:text-void transition-colors py-1">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                     :class="mostrarMais ? 'rotate-90' : ''"
                                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                                Mais detalhes
                                <span class="text-[10px] font-normal text-muted/70">(opcional)</span>
                            </button>
                        </div>

                        {{-- Campos adicionais --}}
                        <div x-show="mostrarMais"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-3">

                            {{-- Chassi / KM --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-muted mb-1.5">Chassi</label>
                                    <input type="text" name="chassi" x-model="form.chassi" placeholder="9BWZZZ..."
                                           class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors uppercase">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-muted mb-1.5">KM atual</label>
                                    <input type="text" name="km" inputmode="numeric" x-model="form.km" placeholder="0"
                                           class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void placeholder-muted focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                </div>
                            </div>

                            {{-- Combustível / Câmbio --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-muted mb-1.5">Combustível</label>
                                    <select name="combustivel" x-model="form.combustivel"
                                            class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                        <option value="">—</option>
                                        <option>Flex</option>
                                        <option>Gasolina</option>
                                        <option>Etanol</option>
                                        <option>Diesel</option>
                                        <option>GNV</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-muted mb-1.5">Câmbio</label>
                                    <select name="cambio" x-model="form.cambio"
                                            class="w-full px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-void focus:outline-none focus:ring-2 focus:border-spark transition-colors">
                                        <option value="">—</option>
                                        <option>Manual</option>
                                        <option>Automático</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    {{-- Footer do modal --}}
                    <div class="flex items-center justify-end gap-3 px-6 py-4 flex-shrink-0"
                         style="border-top: 1px solid var(--color-border);">
                        <button type="button" @click="fecharModal()"
                                class="px-4 py-2 rounded-lg border border-border text-void text-sm font-medium hover:bg-surface transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                :disabled="!formValido"
                                :class="formValido
                                    ? 'bg-spark text-white hover:bg-spark/90 cursor-pointer'
                                    : 'bg-border text-muted cursor-not-allowed'"
                                class="px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                            Cadastrar
                        </button>
                    </div>
                    </form>

                </div>
            </div>
        </template>

    </div>

    <script>
        function veiculosPage() {
            return {
                veiculos: [],
                etapas:   {},
                clientes: [],
                busca:    '',

                modalAberto: false,
                mostrarMais: false,

                form: {
                    marca: '', modelo: '', placa: '', ano: '', cor: '',
                    cliente_id: '', chassi: '', km: '', combustivel: '', cambio: '',
                },

                init() {
                    this.veiculos = window.__veiculos || [];
                    this.etapas   = window.__etapas   || {};
                    this.clientes = window.__clientes || [];

                    @if ($errors->any())
                        this.form = {
                            marca:       {!! json_encode(old('marca', '')) !!},
                            modelo:      {!! json_encode(old('modelo', '')) !!},
                            placa:       {!! json_encode(old('placa', '')) !!},
                            ano:         {!! json_encode(old('ano', '')) !!},
                            cor:         {!! json_encode(old('cor', '')) !!},
                            cliente_id:  {!! json_encode(old('cliente_id', '')) !!},
                            chassi:      {!! json_encode(old('chassi', '')) !!},
                            km:          {!! json_encode(old('km', '')) !!},
                            combustivel: {!! json_encode(old('combustivel', '')) !!},
                            cambio:      {!! json_encode(old('cambio', '')) !!},
                        };
                        this.mostrarMais = true;
                        this.modalAberto = true;
                    @endif
                },

                get veiculosFiltrados() {
                    if (!this.busca.trim()) return this.veiculos;
                    const q = this.busca.toLowerCase();
                    return this.veiculos.filter(v =>
                        v.placa.toLowerCase().includes(q) ||
                        (v.marca + ' ' + v.modelo).toLowerCase().includes(q) ||
                        (v.cliente_nome || '').toLowerCase().includes(q)
                    );
                },

                etapaInfo(key) {
                    return this.etapas[key] || { label: key, cor: '#94A3B8' };
                },

                get formValido() {
                    return this.form.marca.trim() !== ''
                        && this.form.modelo.trim() !== ''
                        && this.form.placa.trim() !== '';
                },

                abrirModal() {
                    this.form = {
                        marca: '', modelo: '', placa: '', ano: '', cor: '',
                        cliente_id: '', chassi: '', km: '', combustivel: '', cambio: '',
                    };
                    this.mostrarMais = false;
                    this.modalAberto = true;
                },

                fecharModal() {
                    this.modalAberto = false;
                },
            };
        }
    </script>

</x-layouts.oficina>
