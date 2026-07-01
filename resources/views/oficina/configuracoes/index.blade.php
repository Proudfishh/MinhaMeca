<x-layouts.oficina title="Configurações">

    <script>
        window.__config = {!! json_encode($config, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
    </script>

    <div x-data="configPage()" x-init="init()">

        {{-- Cabeçalho --}}
        <div class="mb-6">
            <h2 class="font-display font-bold text-void text-xl">Configurações</h2>
            <p class="text-muted text-sm mt-1">Gerencie sua conta e as configurações da plataforma.</p>
        </div>

        @php
            $abas = [
                ['key' => 'conta',      'label' => 'Minha Conta', 'dono' => false, 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
                ['key' => 'plataforma', 'label' => 'Plataforma',  'dono' => true,  'icon' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z'],
                ['key' => 'equipe',     'label' => 'Equipe',      'dono' => true,  'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
                ['key' => 'assinatura', 'label' => 'Assinatura',  'dono' => true,  'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z'],
            ];
        @endphp

        {{-- Abas: DESKTOP (barra de pílulas) --}}
        <div class="hidden md:flex gap-1 mb-6 p-1 rounded-xl w-fit"
             style="background: rgba(15,23,42,0.06); border: 1px solid var(--color-border);">
            @foreach($abas as $aba)
                <button @click="tab = '{{ $aba['key'] }}'"
                        :class="tab === '{{ $aba['key'] }}' ? 'bg-white text-void shadow-sm' : 'text-muted hover:text-void'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all">
                    {{ $aba['label'] }}
                    @if($aba['dono'])
                        <span class="text-[10px] px-1.5 py-0.5 rounded font-semibold"
                              style="background: rgba(124,58,237,0.1); color: #7C3AED;">Dono</span>
                    @endif
                    @if($aba['key'] === 'equipe')
                        <span x-show="pendentes.length > 0"
                              class="w-4 h-4 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center"
                              x-text="pendentes.length"></span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Abas: MOBILE (grade de ícones 2×2) --}}
        <div class="md:hidden grid grid-cols-2 gap-2 mb-6">
            @foreach($abas as $aba)
                <button @click="tab = '{{ $aba['key'] }}'"
                        :class="tab === '{{ $aba['key'] }}' ? 'bg-white shadow-sm border-transparent' : 'bg-surface border-border'"
                        class="relative flex items-center gap-2.5 px-3 py-3 rounded-xl border transition-all text-left">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors"
                          :class="tab === '{{ $aba['key'] }}' ? 'bg-spark/10' : 'bg-white'"
                          :style="tab === '{{ $aba['key'] }}' ? '' : 'border:1px solid var(--color-border)'">
                        <svg class="w-4 h-4 transition-colors"
                             :class="tab === '{{ $aba['key'] }}' ? 'text-spark' : 'text-muted'"
                             fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $aba['icon'] }}"/>
                        </svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-medium leading-tight"
                              :class="tab === '{{ $aba['key'] }}' ? 'text-void' : 'text-muted'">{{ $aba['label'] }}</span>
                        @if($aba['dono'])
                            <span class="inline-block mt-0.5 text-[9px] px-1.5 py-0.5 rounded font-semibold"
                                  style="background: rgba(124,58,237,0.1); color: #7C3AED;">Dono</span>
                        @endif
                    </span>
                    @if($aba['key'] === 'equipe')
                        <span x-show="pendentes.length > 0"
                              class="absolute top-2 right-2 w-4 h-4 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center"
                              x-text="pendentes.length"></span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- ============================================================ --}}
        {{-- ABA: MINHA CONTA --}}
        {{-- ============================================================ --}}
        <div x-show="tab === 'conta'" class="space-y-6">

            {{-- Perfil --}}
            <div class="bg-white rounded-xl border border-border p-6">
                <h3 class="font-semibold text-void text-base mb-4">Perfil</h3>
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-16 h-16 rounded-full bg-ocean flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xl font-bold" x-text="conta.nome.charAt(0)"></span>
                    </div>
                    <div>
                        <button class="text-sm text-spark hover:text-spark/80 font-medium transition-colors">Alterar foto</button>
                        <p class="text-xs text-muted mt-0.5">JPG, PNG ou GIF · Máx. 2MB</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Nome completo</label>
                        <input type="text" x-model="conta.nome"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Telefone</label>
                        <input type="text" x-model="conta.telefone"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-muted mb-1.5">E-mail</label>
                        <div class="relative">
                            <input type="email" x-model="conta.email" readonly
                                   class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-muted bg-surface focus:outline-none cursor-not-allowed pr-24">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] px-2 py-0.5 rounded-full font-semibold"
                                  style="background: rgba(16,185,129,0.1); color: #10B981;">verificado</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button @click="salvar('perfil')"
                            class="px-4 py-2 bg-spark text-white text-sm font-medium rounded-lg hover:bg-spark/90 transition-colors">
                        Salvar perfil
                    </button>
                </div>
            </div>

            {{-- Segurança --}}
            <div class="bg-white rounded-xl border border-border p-6">
                <h3 class="font-semibold text-void text-base mb-4">Segurança</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-5">
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Senha atual</label>
                        <input type="password" placeholder="••••••••"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm focus:outline-none focus:border-spark transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Nova senha</label>
                        <input type="password" placeholder="••••••••"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm focus:outline-none focus:border-spark transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Confirmar nova senha</label>
                        <input type="password" placeholder="••••••••"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm focus:outline-none focus:border-spark transition-colors">
                    </div>
                </div>

                {{-- Sessões --}}
                <div class="rounded-lg border border-border p-4 mb-4" style="background: rgba(248,250,252,0.8);">
                    <p class="text-xs font-semibold text-muted uppercase tracking-wide mb-3">Sessões ativas</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-muted" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-void font-medium">Chrome · Windows</p>
                                <p class="text-xs text-muted">São Paulo, SP · Agora mesmo</p>
                            </div>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-medium"
                              style="background: rgba(16,185,129,0.1); color: #10B981;">Esta sessão</span>
                    </div>
                </div>

                {{-- 2FA --}}
                <div class="flex items-center justify-between py-3 px-4 rounded-lg border border-border opacity-50">
                    <div>
                        <p class="text-sm font-medium text-void">Autenticação em dois fatores</p>
                        <p class="text-xs text-muted">Em breve disponível</p>
                    </div>
                    <div class="w-10 h-6 rounded-full bg-border cursor-not-allowed"></div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button @click="salvar('senha')"
                            class="px-4 py-2 bg-spark text-white text-sm font-medium rounded-lg hover:bg-spark/90 transition-colors">
                        Alterar senha
                    </button>
                </div>
            </div>

            {{-- Notificações --}}
            <div class="bg-white rounded-xl border border-border p-6">
                <h3 class="font-semibold text-void text-base mb-1">Notificações</h3>
                <p class="text-xs text-muted mb-4">Escolha quais alertas deseja receber.</p>
                <div class="space-y-3">
                    @foreach([
                        ['key' => 'os_concluida',      'label' => 'OS concluída e pronta para retirada'],
                        ['key' => 'garantia_vencendo', 'label' => 'Garantia vencendo em 10 dias'],
                        ['key' => 'estoque_baixo',     'label' => 'Estoque abaixo do mínimo'],
                        ['key' => 'pendencia_vencida', 'label' => 'Pendência financeira vencida'],
                        ['key' => 'novo_funcionario',  'label' => 'Novo cadastro de funcionário aguardando aprovação'],
                    ] as $notif)
                        <label class="flex items-center justify-between cursor-pointer group">
                            <span class="text-sm text-void group-hover:text-spark transition-colors">{{ $notif['label'] }}</span>
                            <button type="button"
                                    @click="conta.notificacoes.{{ $notif['key'] }} = !conta.notificacoes.{{ $notif['key'] }}"
                                    :class="conta.notificacoes.{{ $notif['key'] }} ? 'bg-spark' : 'bg-border'"
                                    class="relative w-10 h-6 rounded-full transition-colors flex-shrink-0 ml-4">
                                <span :style="conta.notificacoes.{{ $notif['key'] }} ? 'left:20px' : 'left:4px'"
                                      class="absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-all"></span>
                            </button>
                        </label>
                    @endforeach
                </div>
                <div class="mt-5 flex justify-end">
                    <button @click="salvar('notificacoes')"
                            class="px-4 py-2 bg-spark text-white text-sm font-medium rounded-lg hover:bg-spark/90 transition-colors">
                        Salvar preferências
                    </button>
                </div>
            </div>

            {{-- Aparência --}}
            <div class="bg-white rounded-xl border border-border p-6">
                <h3 class="font-semibold text-void text-base mb-4">Aparência</h3>
                <div class="space-y-4">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <p class="text-sm font-medium text-void">Tema escuro</p>
                            <p class="text-xs text-muted">Em breve disponível</p>
                        </div>
                        <div class="w-10 h-6 rounded-full bg-border opacity-50 cursor-not-allowed"></div>
                    </label>
                    <div style="border-top: 1px solid var(--color-border);" class="pt-4">
                        <p class="text-sm font-medium text-void mb-2">Idioma</p>
                        <div class="flex gap-2 flex-wrap">
                            <button class="px-3 py-1.5 rounded-lg text-sm font-medium bg-spark/10 text-spark border border-spark/20">
                                Português (BR)
                            </button>
                            @foreach(['English', 'Español'] as $idioma)
                                <button class="px-3 py-1.5 rounded-lg text-sm text-muted border border-border flex items-center gap-1.5 opacity-50 cursor-not-allowed">
                                    {{ $idioma }}
                                    <span class="text-[9px] px-1 py-0.5 rounded bg-border font-semibold">Em breve</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /conta --}}

        {{-- ============================================================ --}}
        {{-- ABA: PLATAFORMA --}}
        {{-- ============================================================ --}}
        <div x-show="tab === 'plataforma'" class="space-y-6">

            {{-- Dados da oficina --}}
            <div class="bg-white rounded-xl border border-border p-6">
                <h3 class="font-semibold text-void text-base mb-4">Dados da oficina</h3>
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-16 h-16 rounded-xl border-2 border-dashed border-border flex items-center justify-center bg-surface">
                        <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                    </div>
                    <div>
                        <button class="text-sm text-spark hover:text-spark/80 font-medium transition-colors">Alterar logo</button>
                        <p class="text-xs text-muted mt-0.5">PNG, SVG · Recomendado 200×200px</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Nome da oficina</label>
                        <input type="text" x-model="plataforma.oficina.nome"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">CNPJ</label>
                        <input type="text" x-model="plataforma.oficina.cnpj"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Telefone</label>
                        <input type="text" x-model="plataforma.oficina.telefone"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">E-mail de contato</label>
                        <input type="email" x-model="plataforma.oficina.email"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-muted mb-1.5">Site</label>
                        <input type="text" x-model="plataforma.oficina.site"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Logradouro</label>
                        <input type="text" x-model="plataforma.oficina.logradouro"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Número</label>
                        <input type="text" x-model="plataforma.oficina.numero"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Bairro</label>
                        <input type="text" x-model="plataforma.oficina.bairro"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">CEP</label>
                        <input type="text" x-model="plataforma.oficina.cep"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Cidade</label>
                        <input type="text" x-model="plataforma.oficina.cidade"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">UF</label>
                        <input type="text" x-model="plataforma.oficina.uf" maxlength="2"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors uppercase">
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button @click="salvar('dados')"
                            class="px-4 py-2 bg-spark text-white text-sm font-medium rounded-lg hover:bg-spark/90 transition-colors">
                        Salvar dados
                    </button>
                </div>
            </div>

            {{-- Horário --}}
            <div class="bg-white rounded-xl border border-border p-6">
                <h3 class="font-semibold text-void text-base mb-4">Horário de funcionamento</h3>
                <div class="space-y-2">
                    <template x-for="(dia, idx) in plataforma.horario" :key="idx">
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 py-2 rounded-lg px-2 transition-colors"
                             :class="dia.ativo ? '' : 'opacity-40'">
                            <button type="button"
                                    @click="dia.ativo = !dia.ativo"
                                    :class="dia.ativo ? 'bg-spark' : 'bg-border'"
                                    class="relative w-9 h-5 rounded-full transition-colors flex-shrink-0">
                                <span :style="dia.ativo ? 'left:16px' : 'left:2px'"
                                      class="absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-all"></span>
                            </button>
                            <span class="text-sm text-void w-24 sm:w-32 flex-shrink-0" x-text="dia.dia"></span>
                            <div class="flex items-center gap-2 w-full sm:w-auto sm:flex-1 pl-12 sm:pl-0">
                                <input type="time" x-model="dia.abertura" :disabled="!dia.ativo"
                                       class="rounded-lg border border-border px-2 py-1.5 text-sm text-void focus:outline-none focus:border-spark transition-colors disabled:cursor-not-allowed">
                                <span class="text-muted text-xs">até</span>
                                <input type="time" x-model="dia.fechamento" :disabled="!dia.ativo"
                                       class="rounded-lg border border-border px-2 py-1.5 text-sm text-void focus:outline-none focus:border-spark transition-colors disabled:cursor-not-allowed">
                            </div>
                        </div>
                    </template>
                </div>
                <div class="mt-4 flex justify-end">
                    <button @click="salvar('horario')"
                            class="px-4 py-2 bg-spark text-white text-sm font-medium rounded-lg hover:bg-spark/90 transition-colors">
                        Salvar horário
                    </button>
                </div>
            </div>

            {{-- Config OS --}}
            <div class="bg-white rounded-xl border border-border p-6">
                <h3 class="font-semibold text-void text-base mb-4">Ordens de Serviço</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Prazo padrão de garantia (dias)</label>
                        <input type="number" x-model="plataforma.os_config.prazo_garantia" min="1" max="365"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                        <p class="text-xs text-muted mt-1">Aplicado automaticamente a todas as OS finalizadas.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Prefixo do número de OS</label>
                        <input type="text" x-model="plataforma.os_config.prefixo_os"
                               class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors font-mono">
                        <p class="text-xs text-muted mt-1">Ex: <span class="font-mono" x-text="plataforma.os_config.prefixo_os + '0001'"></span></p>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button @click="salvar('os')"
                            class="px-4 py-2 bg-spark text-white text-sm font-medium rounded-lg hover:bg-spark/90 transition-colors">
                        Salvar configurações de OS
                    </button>
                </div>
            </div>

            {{-- Portal do cliente --}}
            <div class="bg-white rounded-xl border border-border p-6">
                <h3 class="font-semibold text-void text-base mb-1">Portal do Cliente</h3>
                <p class="text-xs text-muted mb-4">Personaliza o que o cliente vê no portal de acompanhamento.</p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Mensagem de boas-vindas</label>
                        <textarea x-model="plataforma.portal.mensagem_boas_vindas" rows="3"
                                  class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors resize-none"></textarea>
                    </div>
                    <div style="border-top: 1px solid var(--color-border);" class="pt-4 space-y-3">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-sm text-void">Exibir previsão de entrega para o cliente</span>
                            <button type="button"
                                    @click="plataforma.portal.exibir_previsao_entrega = !plataforma.portal.exibir_previsao_entrega"
                                    :class="plataforma.portal.exibir_previsao_entrega ? 'bg-spark' : 'bg-border'"
                                    class="relative w-10 h-6 rounded-full transition-colors flex-shrink-0 ml-4">
                                <span :style="plataforma.portal.exibir_previsao_entrega ? 'left:20px' : 'left:4px'"
                                      class="absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-all"></span>
                            </button>
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-sm text-void">Exibir lista de serviços para o cliente</span>
                            <button type="button"
                                    @click="plataforma.portal.exibir_lista_servicos = !plataforma.portal.exibir_lista_servicos"
                                    :class="plataforma.portal.exibir_lista_servicos ? 'bg-spark' : 'bg-border'"
                                    class="relative w-10 h-6 rounded-full transition-colors flex-shrink-0 ml-4">
                                <span :style="plataforma.portal.exibir_lista_servicos ? 'left:20px' : 'left:4px'"
                                      class="absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-all"></span>
                            </button>
                        </label>
                    </div>
                </div>
                <div class="mt-5 flex justify-end">
                    <button @click="salvar('portal')"
                            class="px-4 py-2 bg-spark text-white text-sm font-medium rounded-lg hover:bg-spark/90 transition-colors">
                        Salvar configurações do portal
                    </button>
                </div>
            </div>

        </div>{{-- /plataforma --}}

        {{-- ============================================================ --}}
        {{-- ABA: EQUIPE --}}
        {{-- ============================================================ --}}
        <div x-show="tab === 'equipe'" class="space-y-6">

            {{-- Aprovações pendentes --}}
            <template x-if="pendentes.length > 0">
                <div class="bg-white rounded-xl border border-border p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <h3 class="font-semibold text-void text-base">Aprovações pendentes</h3>
                        <span class="w-5 h-5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center"
                              x-text="pendentes.length"></span>
                    </div>
                    <div class="space-y-3">
                        <template x-for="(p, idx) in pendentes" :key="p.id">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 p-4 rounded-xl"
                                 style="background: rgba(248,250,252,0.8); border: 1px solid var(--color-border);">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="w-10 h-10 rounded-full bg-ocean/20 flex items-center justify-center flex-shrink-0">
                                        <span class="text-ocean font-bold text-sm" x-text="p.nome.charAt(0)"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-void" x-text="p.nome"></p>
                                        <p class="text-xs text-muted truncate" x-text="p.email"></p>
                                        <p class="text-xs text-muted mt-0.5" x-text="'Cadastrado em ' + formatarData(p.data)"></p>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 flex-shrink-0">
                                    <select x-model="p._papel"
                                            class="w-full sm:w-auto rounded-lg border border-border px-2 py-1.5 text-xs text-void focus:outline-none focus:border-spark transition-colors">
                                        <option value="">Selecionar papel</option>
                                        <option value="gerente">Gerente</option>
                                        <option value="mecanico">Mecânico</option>
                                        <option value="recepcao">Recepção</option>
                                        <option value="financeiro">Financeiro</option>
                                        <option value="vendedor">Vendedor</option>
                                    </select>
                                    <div class="flex items-center gap-2">
                                        <button @click="aprovarMembro(idx)"
                                                :disabled="!p._papel"
                                                class="flex-1 sm:flex-none px-3 py-1.5 text-xs font-medium rounded-lg transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                                                style="background: rgba(16,185,129,0.1); color: #10B981; border: 1px solid rgba(16,185,129,0.2);"
                                                onmouseover="if(!this.disabled){this.style.background='rgba(16,185,129,0.2)'}"
                                                onmouseout="this.style.background='rgba(16,185,129,0.1)'">
                                            Aprovar
                                        </button>
                                        <button @click="rejeitarMembro(idx)"
                                                class="flex-1 sm:flex-none px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
                                                style="background: rgba(239,68,68,0.08); color: #EF4444; border: 1px solid rgba(239,68,68,0.15);"
                                                onmouseover="this.style.background='rgba(239,68,68,0.15)'"
                                                onmouseout="this.style.background='rgba(239,68,68,0.08)'">
                                            Rejeitar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Membros --}}
            <div class="bg-white rounded-xl border border-border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-void text-base">Membros da equipe</h3>
                    <button @click="modalFuncAberto = true"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-spark text-white text-xs font-medium rounded-lg hover:bg-spark/90 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Cadastrar funcionário
                    </button>
                </div>

                {{-- DESKTOP: tabela --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <th class="text-left text-xs font-semibold text-muted uppercase tracking-wide pb-3 pr-4">Membro</th>
                                <th class="text-left text-xs font-semibold text-muted uppercase tracking-wide pb-3 pr-4">Papel</th>
                                <th class="text-left text-xs font-semibold text-muted uppercase tracking-wide pb-3 pr-4">Status</th>
                                <th class="text-right text-xs font-semibold text-muted uppercase tracking-wide pb-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="m in membros" :key="m.id">
                                <tr style="border-bottom: 1px solid var(--color-border);" class="group">
                                    <td class="py-3 pr-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                                 :style="'background:' + papelCor(m.papel) + '20'">
                                                <span class="text-xs font-bold" :style="'color:' + papelCor(m.papel)" x-text="m.nome.charAt(0)"></span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-void text-sm" x-text="m.nome"></p>
                                                <p class="text-xs text-muted" x-text="m.email"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                              :style="'background:' + papelCor(m.papel) + '18; color:' + papelCor(m.papel)"
                                              x-text="papelLabel(m.papel)"></span>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                              :class="m.status === 'ativo' ? 'text-emerald-700 bg-emerald-50' : 'text-muted bg-surface'"
                                              x-text="m.status === 'ativo' ? 'Ativo' : 'Inativo'"></span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <template x-if="m.papel !== 'dono'">
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button @click="salvar('membro')"
                                                        class="text-xs text-spark hover:text-spark/70 font-medium transition-colors">
                                                    Editar papel
                                                </button>
                                                <span class="text-border">·</span>
                                                <button @click="salvar('membro')"
                                                        class="text-xs text-muted hover:text-red-500 transition-colors">
                                                    Desativar
                                                </button>
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE: cards --}}
                <div class="md:hidden space-y-2">
                    <template x-for="m in membros" :key="m.id">
                        <div class="rounded-xl border border-border p-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0"
                                     :style="'background:' + papelCor(m.papel) + '20'">
                                    <span class="text-sm font-bold" :style="'color:' + papelCor(m.papel)" x-text="m.nome.charAt(0)"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-void text-sm truncate" x-text="m.nome"></p>
                                    <p class="text-xs text-muted truncate" x-text="m.email"></p>
                                </div>
                                <template x-if="m.papel !== 'dono'">
                                    <button @click="sheetMembroId = m.id"
                                            class="w-8 h-8 rounded-lg border border-border text-muted flex items-center justify-center flex-shrink-0 active:bg-surface"
                                            aria-label="Ações do membro">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8a2 2 0 100-4 2 2 0 000 4zm0 6a2 2 0 100-4 2 2 0 000 4zm0 6a2 2 0 100-4 2 2 0 000 4z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <div class="flex items-center gap-2 mt-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                      :style="'background:' + papelCor(m.papel) + '18; color:' + papelCor(m.papel)"
                                      x-text="papelLabel(m.papel)"></span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                      :class="m.status === 'ativo' ? 'text-emerald-700 bg-emerald-50' : 'text-muted bg-surface'"
                                      x-text="m.status === 'ativo' ? 'Ativo' : 'Inativo'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>{{-- /equipe --}}

        {{-- ============================================================ --}}
        {{-- ABA: ASSINATURA --}}
        {{-- ============================================================ --}}
        <div x-show="tab === 'assinatura'" class="space-y-6">

            {{-- Plano atual --}}
            <div class="bg-white rounded-xl border border-border p-6">
                <h3 class="font-semibold text-void text-base mb-4">Plano atual</h3>
                <div class="rounded-xl p-5 mb-4" style="background: linear-gradient(135deg, rgba(30,58,95,0.08), rgba(59,130,246,0.06)); border: 1px solid rgba(30,58,95,0.15);">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-display font-bold text-void text-xl" x-text="assinatura.plano"></p>
                            <p class="text-muted text-xs mt-1">
                                Renova em <strong class="text-void" x-text="formatarData(assinatura.renovacao)"></strong>
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-void text-lg font-mono">
                                R$ <span x-text="assinatura.valor.toFixed(2).replace('.', ',')"></span>
                            </p>
                            <p class="text-muted text-xs">por mês</p>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-1.5">
                        <template x-for="r in assinatura.recursos" :key="r">
                            <div class="flex items-center gap-1.5 text-xs text-void">
                                <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span x-text="r"></span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 text-sm text-emerald-600 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                        Assinatura ativa
                    </span>
                    <button @click="modalUpgradeAberto = true"
                            class="px-4 py-2 bg-spark text-white text-sm font-medium rounded-lg hover:bg-spark/90 transition-colors">
                        Fazer upgrade
                    </button>
                </div>
            </div>

            {{-- Pagamento --}}
            <div class="bg-white rounded-xl border border-border p-6">
                <h3 class="font-semibold text-void text-base mb-4">Método de pagamento</h3>
                <div class="flex items-center justify-between p-4 rounded-xl"
                     style="background: rgba(248,250,252,0.8); border: 1px solid var(--color-border);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-7 rounded bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-[9px] font-bold">VISA</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-void">•••• •••• •••• <span x-text="assinatura.cartao.ultimos4"></span></p>
                            <p class="text-xs text-muted">Expira <span x-text="assinatura.cartao.validade"></span></p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button @click="mostrarToast('Funcionalidade disponível em breve')"
                                class="text-xs text-spark hover:text-spark/70 font-medium transition-colors">
                            Alterar
                        </button>
                        <span class="text-border">·</span>
                        <button @click="mostrarToast('Funcionalidade disponível em breve')"
                                class="text-xs text-muted hover:text-red-500 transition-colors">
                            Remover
                        </button>
                    </div>
                </div>
                <p class="text-xs text-muted mt-3 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Gerenciado com segurança via gateway de pagamento
                </p>
            </div>

            {{-- Faturas --}}
            <div class="bg-white rounded-xl border border-border p-6">
                <h3 class="font-semibold text-void text-base mb-4">Histórico de faturas</h3>

                {{-- DESKTOP: tabela --}}
                <table class="hidden md:table w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--color-border);">
                            <th class="text-left text-xs font-semibold text-muted uppercase tracking-wide pb-3 pr-4">Data</th>
                            <th class="text-left text-xs font-semibold text-muted uppercase tracking-wide pb-3 pr-4">Período</th>
                            <th class="text-left text-xs font-semibold text-muted uppercase tracking-wide pb-3 pr-4">Valor</th>
                            <th class="text-left text-xs font-semibold text-muted uppercase tracking-wide pb-3 pr-4">Status</th>
                            <th class="text-right text-xs font-semibold text-muted uppercase tracking-wide pb-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="f in assinatura.faturas" :key="f.data">
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <td class="py-3 pr-4 text-void" x-text="formatarData(f.data)"></td>
                                <td class="py-3 pr-4 text-void" x-text="f.periodo"></td>
                                <td class="py-3 pr-4 text-void font-mono" x-text="'R$ ' + f.valor.toFixed(2).replace('.', ',')"></td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-emerald-700 bg-emerald-50"
                                          x-text="f.status"></span>
                                </td>
                                <td class="py-3 text-right">
                                    <button @click="mostrarToast('PDF disponível em breve')"
                                            class="text-xs text-spark hover:text-spark/70 font-medium transition-colors flex items-center gap-1 ml-auto">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        PDF
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                {{-- MOBILE: cards --}}
                <div class="md:hidden space-y-2">
                    <template x-for="f in assinatura.faturas" :key="f.data">
                        <div class="rounded-xl border border-border p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-void" x-text="formatarData(f.data)"></p>
                                    <p class="text-xs text-muted" x-text="f.periodo"></p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-mono font-medium text-void" x-text="'R$ ' + f.valor.toFixed(2).replace('.', ',')"></p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-emerald-700 bg-emerald-50 mt-0.5"
                                          x-text="f.status"></span>
                                </div>
                            </div>
                            <div class="mt-2.5 pt-2.5" style="border-top:1px solid var(--color-border);">
                                <button @click="mostrarToast('PDF disponível em breve')"
                                        class="text-xs text-spark font-medium transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Baixar PDF
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <p class="text-xs text-muted mt-4">Faturas geradas automaticamente a cada ciclo de cobrança.</p>
            </div>

            {{-- Zona de risco --}}
            <div class="rounded-xl p-4 flex items-center justify-between gap-4"
                 style="border: 1px solid rgba(239,68,68,0.25); background: rgba(239,68,68,0.03);">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-void">Cancelar assinatura</p>
                    <p class="text-xs text-muted mt-0.5">Encerra ao fim do período já pago. Você mantém o acesso até lá.</p>
                </div>
                <button @click="modalCancelarAberto = true"
                        class="flex-shrink-0 px-3 py-2 rounded-lg text-xs font-semibold transition-colors"
                        style="color:#ef4444; border:1px solid rgba(239,68,68,0.35);"
                        onmouseover="this.style.background='rgba(239,68,68,0.06)'"
                        onmouseout="this.style.background='transparent'">
                    Cancelar
                </button>
            </div>

        </div>{{-- /assinatura --}}

        {{-- ============================================================ --}}
        {{-- MODAL: CADASTRAR FUNCIONÁRIO --}}
        {{-- ============================================================ --}}
        <template x-teleport="body">
            <div x-show="modalFuncAberto"
                 @keydown.escape.window="modalFuncAberto = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 flex items-center justify-center p-4">
                <div @click="modalFuncAberto = false" class="absolute inset-0 bg-void/50"></div>
                <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-md p-6"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <h3 class="font-display font-semibold text-void text-lg mb-1">Cadastrar funcionário</h3>
                    <p class="text-muted text-sm mb-5">O funcionário é adicionado direto à equipe, já ativo.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Nome completo <span class="text-spark">*</span></label>
                            <input type="text" x-model="func.nome" placeholder="Ex: João da Silva"
                                   class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">E-mail <span class="text-spark">*</span></label>
                            <input type="email" x-model="func.email" placeholder="funcionario@email.com"
                                   class="w-full rounded-lg border border-border px-3 py-2.5 text-sm focus:outline-none focus:border-spark transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted mb-1.5">Papel <span class="text-spark">*</span></label>
                            <select x-model="func.papel"
                                    class="w-full rounded-lg border border-border px-3 py-2.5 text-sm text-void focus:outline-none focus:border-spark transition-colors">
                                <option value="">Selecionar papel</option>
                                <option value="gerente">Gerente</option>
                                <option value="mecanico">Mecânico</option>
                                <option value="recepcao">Recepção</option>
                                <option value="financeiro">Financeiro</option>
                                <option value="vendedor">Vendedor</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button @click="modalFuncAberto = false"
                                class="flex-1 px-4 py-2.5 rounded-lg border border-border text-sm font-medium text-muted hover:text-void transition-colors">
                            Cancelar
                        </button>
                        <button @click="cadastrarFuncionario()"
                                :disabled="!func.nome || !func.email || !func.papel"
                                class="flex-1 px-4 py-2.5 rounded-lg bg-spark text-white text-sm font-medium hover:bg-spark/90 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                            Cadastrar
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ============================================================ --}}
        {{-- MODAL: CANCELAR ASSINATURA --}}
        {{-- ============================================================ --}}
        <template x-teleport="body">
            <div x-show="modalCancelarAberto"
                 @keydown.escape.window="modalCancelarAberto = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 flex items-center justify-center p-4">
                <div @click="modalCancelarAberto = false" class="absolute inset-0 bg-void/50"></div>
                <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-md p-6"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background: rgba(239,68,68,0.1);">
                        <svg class="w-6 h-6" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-void text-lg mb-1">Cancelar assinatura?</h3>
                    <p class="text-muted text-sm mb-5">
                        Sua assinatura permanece ativa até <strong class="text-void" x-text="formatarData(assinatura.renovacao)"></strong>.
                        Após essa data você perde o acesso aos recursos do plano.
                    </p>
                    <div class="flex gap-3">
                        <button @click="modalCancelarAberto = false"
                                class="flex-1 px-4 py-2.5 rounded-lg border border-border text-sm font-medium text-muted hover:text-void transition-colors">
                            Manter assinatura
                        </button>
                        <button @click="cancelarAssinatura()"
                                class="flex-1 px-4 py-2.5 rounded-lg text-white text-sm font-medium transition-colors"
                                style="background:#ef4444;">
                            Cancelar assinatura
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ============================================================ --}}
        {{-- MODAL: UPGRADE --}}
        {{-- ============================================================ --}}
        <template x-teleport="body">
            <div x-show="modalUpgradeAberto"
                 @keydown.escape.window="modalUpgradeAberto = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 flex items-center justify-center p-4">
                <div @click="modalUpgradeAberto = false" class="absolute inset-0 bg-void/50"></div>
                <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <h3 class="font-display font-semibold text-void text-lg mb-1">Planos disponíveis</h3>
                    <p class="text-muted text-sm mb-6">Escolha o plano ideal para sua oficina.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach([
                            ['nome' => 'Básico',        'valor' => '79,90',  'recursos' => ['Até 2 usuários', '50 OSes/mês', 'Portal do cliente']],
                            ['nome' => 'Profissional',  'valor' => '149,90', 'recursos' => ['Usuários ilimitados', 'OSes ilimitadas', 'Garantias', 'Estoque', 'Relatórios']],
                            ['nome' => 'Enterprise',    'valor' => '299,90', 'recursos' => ['Tudo do Profissional', 'Multi-filial', 'API', 'Suporte prioritário']],
                        ] as $plano)
                            <div class="rounded-xl p-4 border-2 {{ $plano['nome'] === 'Profissional' ? 'border-spark' : 'border-border' }}">
                                @if($plano['nome'] === 'Profissional')
                                    <span class="inline-block text-[10px] px-2 py-0.5 rounded-full font-bold mb-2"
                                          style="background: rgba(59,130,246,0.1); color: #3B82F6;">Plano atual</span>
                                @else
                                    <span class="inline-block text-[10px] px-2 py-0.5 rounded-full font-bold mb-2 bg-surface text-muted">Em breve</span>
                                @endif
                                <p class="font-display font-bold text-void text-base">{{ $plano['nome'] }}</p>
                                <p class="text-2xl font-bold text-void mt-1">R$ {{ $plano['valor'] }}<span class="text-sm font-normal text-muted">/mês</span></p>
                                <ul class="mt-3 space-y-1.5">
                                    @foreach($plano['recursos'] as $r)
                                        <li class="flex items-center gap-1.5 text-xs text-void">
                                            <svg class="w-3 h-3 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            {{ $r }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-end mt-6">
                        <button @click="modalUpgradeAberto = false"
                                class="px-4 py-2 border border-border rounded-lg text-sm font-medium text-muted hover:text-void transition-colors">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ============================================================ --}}
        {{-- BOTTOM SHEET: AÇÕES DO MEMBRO (mobile) --}}
        {{-- ============================================================ --}}
        <div x-show="sheetMembroId !== null" x-cloak
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="sheetMembroId = null"
             class="fixed inset-0 z-40 md:hidden" style="background:rgba(0,0,0,0.5);"></div>
        <div x-show="sheetMembroId !== null" x-cloak
             x-transition:enter="transition ease-out duration-250" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 left-0 right-0 z-50 rounded-t-2xl px-4 pb-8 pt-3 md:hidden" style="background:#fff;">
            <div class="flex justify-center mb-3">
                <div class="w-10 h-1 rounded-full" style="background:rgba(0,0,0,0.1);"></div>
            </div>
            <p class="text-center font-semibold text-void text-sm mb-1" x-text="membroSheet().nome"></p>
            <p class="text-center text-xs text-muted mb-4" x-text="papelLabel(membroSheet().papel)"></p>
            <div class="space-y-2">
                <button @click="salvar('membro'); sheetMembroId = null"
                        class="w-full py-3 rounded-xl text-sm font-semibold text-spark transition-colors"
                        style="border:1px solid var(--color-border);background:var(--color-surface);">
                    Editar papel
                </button>
                <button @click="salvar('membro'); sheetMembroId = null"
                        class="w-full py-3 rounded-xl text-sm font-semibold text-white"
                        style="background:#ef4444;">
                    Desativar
                </button>
                <button @click="sheetMembroId = null"
                        class="w-full py-3 rounded-xl text-sm font-medium text-muted transition-colors">
                    Cancelar
                </button>
            </div>
        </div>

        {{-- Toast --}}
        <div x-show="toastVisible"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 px-4 py-3 rounded-xl shadow-lg flex items-center gap-2 text-sm font-medium text-white"
             style="background: #1E3A5F; min-width: 220px; justify-content: center;">
            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span x-text="toastMsg"></span>
        </div>

    </div>

    <script>
        function configPage() {
            return {
                tab: '{{ $tabAtiva }}',

                conta:       {},
                plataforma:  { oficina: {}, horario: [], os_config: {}, portal: {} },
                pendentes:   [],
                membros:     [],
                assinatura:  { cartao: {}, faturas: [], recursos: [] },

                modalFuncAberto: false,
                modalUpgradeAberto: false,
                modalCancelarAberto: false,
                sheetMembroId: null,
                func: { nome: '', email: '', papel: '' },

                toastVisible: false,
                toastMsg:     '',
                _toastTimer:  null,

                papelCores: {
                    dono:       '#7C3AED',
                    gerente:    '#3B82F6',
                    mecanico:   '#F59E0B',
                    recepcao:   '#06B6D4',
                    financeiro: '#10B981',
                    vendedor:   '#EC4899',
                },
                papelLabels: {
                    dono:       'Dono',
                    gerente:    'Gerente',
                    mecanico:   'Mecânico',
                    recepcao:   'Recepção',
                    financeiro: 'Financeiro',
                    vendedor:   'Vendedor',
                },

                init() {
                    const c = window.__config;
                    this.conta      = c.conta;
                    this.plataforma = {
                        oficina:   c.oficina,
                        horario:   c.horario,
                        os_config: c.os_config,
                        portal:    c.portal,
                    };
                    this.pendentes  = (c.equipe.pendentes || []).map(p => ({ ...p, _papel: '' }));
                    this.membros    = c.equipe.membros || [];
                    this.assinatura = c.assinatura;
                },

                papelCor(papel)   { return this.papelCores[papel]  || '#94A3B8'; },
                papelLabel(papel) { return this.papelLabels[papel] || papel; },

                membroSheet() {
                    return this.membros.find(m => m.id === this.sheetMembroId) || {};
                },

                salvar(contexto) {
                    const msgs = {
                        perfil:         'Perfil atualizado!',
                        senha:          'Senha alterada!',
                        notificacoes:   'Preferências salvas!',
                        dados:          'Dados da oficina salvos!',
                        horario:        'Horário salvo!',
                        os:             'Configurações de OS salvas!',
                        portal:         'Configurações do portal salvas!',
                        membro:         'Membro atualizado!',
                    };
                    this.mostrarToast(msgs[contexto] || 'Configurações salvas!');
                },

                aprovarMembro(idx) {
                    const p = this.pendentes[idx];
                    if (!p._papel) return;
                    this.membros.push({
                        id:     Date.now(),
                        nome:   p.nome,
                        email:  p.email,
                        papel:  p._papel,
                        status: 'ativo',
                    });
                    this.pendentes.splice(idx, 1);
                    this.mostrarToast(p.nome + ' aprovado como ' + this.papelLabel(p._papel) + '!');
                },

                rejeitarMembro(idx) {
                    const nome = this.pendentes[idx].nome;
                    this.pendentes.splice(idx, 1);
                    this.mostrarToast('Cadastro de ' + nome + ' rejeitado.');
                },

                cadastrarFuncionario() {
                    if (!this.func.nome || !this.func.email || !this.func.papel) return;
                    this.membros.push({
                        id:     Date.now(),
                        nome:   this.func.nome.trim(),
                        email:  this.func.email.trim(),
                        papel:  this.func.papel,
                        status: 'ativo',
                    });
                    this.modalFuncAberto = false;
                    this.mostrarToast(this.func.nome.trim() + ' cadastrado como ' + this.papelLabel(this.func.papel) + '!');
                    this.func = { nome: '', email: '', papel: '' };
                },

                cancelarAssinatura() {
                    this.modalCancelarAberto = false;
                    this.mostrarToast('Assinatura cancelada. Ativa até ' + this.formatarData(this.assinatura.renovacao) + '.');
                },

                formatarData(data) {
                    if (!data) return '—';
                    const [y, m, d] = data.split('-');
                    return `${d}/${m}/${y}`;
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
