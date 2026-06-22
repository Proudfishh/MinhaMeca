<x-layouts.oficina title="Dashboard">

    {{-- ================================================================
         MOBILE: hero card + alertas + fila + OS cards
         DESKTOP: grade original (métricas + fila/estoque + tabela)
    ================================================================ --}}

    {{-- ==================== MÉTRICAS ==================== --}}

    {{-- DESKTOP: 4 cards brancos --}}
    <div class="hidden md:grid grid-cols-4 gap-4 mb-6">
        @php
            $cards = [
                ['label' => 'OS Abertas',       'valor' => $metricas['abertas'],          'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'cor' => '#3B82F6', 'bg' => 'rgba(59,130,246,0.08)'],
                ['label' => 'Em Andamento',     'valor' => $metricas['em_andamento'],     'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                    'cor' => '#7C3AED', 'bg' => 'rgba(124,58,237,0.08)'],
                ['label' => 'Finalizadas Hoje', 'valor' => $metricas['finalizadas_hoje'], 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                  'cor' => '#10B981', 'bg' => 'rgba(16,185,129,0.08)'],
                ['label' => 'Receita do Mês',   'valor' => 'R$ '.number_format($metricas['receita_mes'], 0, ',', '.'), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'cor' => '#F59E0B', 'bg' => 'rgba(245,158,11,0.08)'],
            ];
        @endphp
        @foreach($cards as $card)
            <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-muted text-xs font-medium uppercase tracking-wide">{{ $card['label'] }}</p>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: {{ $card['bg'] }};">
                        <svg class="w-4 h-4" fill="none" stroke="{{ $card['cor'] }}" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
                        </svg>
                    </div>
                </div>
                <p class="font-display font-bold text-void text-2xl">{{ $card['valor'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- MOBILE: hero card com gradiente --}}
    <div class="md:hidden mb-4">
        <div class="rounded-2xl p-5"
             style="background: linear-gradient(135deg, #1E3A5F 0%, #0F172A 100%);">
            <p class="text-xs font-medium mb-4" style="color: rgba(255,255,255,0.5); letter-spacing: 0.05em; text-transform: uppercase;">
                {{ \Carbon\Carbon::now()->locale('pt_BR')->isoFormat('ddd, D [de] MMMM') }}
            </p>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <p class="font-mono font-bold text-white text-4xl leading-none">{{ $metricas['abertas'] }}</p>
                    <p class="text-xs mt-1.5" style="color: rgba(255,255,255,0.55);">OS Abertas</p>
                </div>
                <div>
                    <p class="font-mono font-bold text-white text-4xl leading-none">{{ $metricas['em_andamento'] }}</p>
                    <p class="text-xs mt-1.5" style="color: rgba(255,255,255,0.55);">Em Andamento</p>
                </div>
                <div>
                    <p class="font-mono font-bold text-white text-4xl leading-none">{{ $metricas['finalizadas_hoje'] }}</p>
                    <p class="text-xs mt-1.5" style="color: rgba(255,255,255,0.55);">Finalizadas Hoje</p>
                </div>
                <div>
                    <p class="font-mono font-bold text-white text-xl leading-tight">
                        R$ {{ number_format($metricas['receita_mes'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs mt-1.5" style="color: rgba(255,255,255,0.55);">Receita do Mês</p>
                </div>
            </div>
        </div>
    </div>

    {{-- MOBILE: banner de alertas (só aparece se houver) --}}
    @if($estoqueBaixo->count() > 0)
        <div class="md:hidden mb-4 rounded-xl px-4 py-3.5"
             style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.35);">
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="#D97706" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold" style="color: #92400E;">Estoque Baixo</p>
                    <div class="mt-1 space-y-0.5">
                        @foreach($estoqueBaixo as $peca)
                            <p class="text-xs" style="color: #B45309;">
                                {{ $peca['descricao'] }} — <span class="font-mono font-semibold">{{ $peca['quantidade'] }} un</span>
                            </p>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('oficina.estoque.index') }}"
                   class="text-xs font-semibold flex-shrink-0 mt-0.5" style="color: #3B82F6;">
                    Ver →
                </a>
            </div>
        </div>
    @endif

    {{-- ==================== FILA + ESTOQUE (desktop) ==================== --}}
    <div class="hidden md:grid grid-cols-3 gap-4 mb-6">

        <div class="col-span-2 bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
            <h2 class="font-display font-semibold text-void text-sm mb-4">Fila por Etapa</h2>
            <div class="space-y-2.5">
                @foreach(\App\Services\Mock\MockOsService::ETAPAS as $key => $etapa)
                    @php $count = count($filaEtapas[$key] ?? []); $max = 5; @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $etapa['cor'] }};"></div>
                        <span class="text-xs text-muted w-36 truncate">{{ $etapa['label'] }}</span>
                        <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background: var(--color-border);">
                            <div class="h-full rounded-full transition-all"
                                 style="width: {{ $count > 0 ? ($count / $max * 100) : 0 }}%; background: {{ $etapa['cor'] }}; opacity: 0.7;"></div>
                        </div>
                        <span class="font-mono text-xs font-medium text-void w-4 text-right">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display font-semibold text-void text-sm">Estoque Baixo</h2>
                @if($estoqueBaixo->count() > 0)
                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded"
                          style="background: rgba(245,158,11,0.12); color: #B45309;">
                        {{ $estoqueBaixo->count() }} alerta{{ $estoqueBaixo->count() > 1 ? 's' : '' }}
                    </span>
                @endif
            </div>
            @if($estoqueBaixo->isEmpty())
                <p class="text-muted text-xs">Estoque dentro do limite mínimo.</p>
            @else
                <div class="space-y-3">
                    @foreach($estoqueBaixo as $peca)
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-xs text-void leading-snug">{{ $peca['descricao'] }}</p>
                            <span class="font-mono text-xs flex-shrink-0" style="color: #B45309;">{{ $peca['quantidade'] }} un</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- MOBILE: fila por etapa --}}
    <div class="md:hidden mb-4 bg-white rounded-xl p-4" style="border: 1px solid var(--color-border);">
        <h2 class="font-display font-semibold text-void text-sm mb-3">Fila por Etapa</h2>
        <div class="space-y-2.5">
            @foreach(\App\Services\Mock\MockOsService::ETAPAS as $key => $etapa)
                @php $count = count($filaEtapas[$key] ?? []); $max = 5; @endphp
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $etapa['cor'] }};"></div>
                    <span class="text-xs text-muted flex-shrink-0 truncate" style="width: 90px;">{{ $etapa['label'] }}</span>
                    <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background: var(--color-border);">
                        <div class="h-full rounded-full"
                             style="width: {{ $count > 0 ? ($count / $max * 100) : 0 }}%; background: {{ $etapa['cor'] }}; opacity: 0.7;"></div>
                    </div>
                    <span class="font-mono text-xs font-medium text-void w-3 text-right">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ==================== OS RECENTES ==================== --}}

    {{-- DESKTOP: tabela --}}
    <div class="hidden md:block bg-white rounded-xl" style="border: 1px solid var(--color-border);">
        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid var(--color-border);">
            <h2 class="font-display font-semibold text-void text-sm">Ordens de Serviço Recentes</h2>
            <a href="{{ route('oficina.os.index') }}" class="text-spark text-xs font-medium hover:underline">Ver todas</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--color-border);">
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">OS</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Cliente</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Veículo</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Etapa</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Mecânico</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Valor</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($os as $ordem)
                        @php $etapa = \App\Services\Mock\MockOsService::ETAPAS[$ordem['etapa_atual']]; @endphp
                        <tr class="hover:bg-surface transition-colors" style="border-bottom: 1px solid var(--color-border);">
                            <td class="px-5 py-3.5"><span class="font-mono text-xs text-muted whitespace-nowrap">{{ $ordem['id'] }}</span></td>
                            <td class="px-5 py-3.5"><span class="text-void font-medium text-sm">{{ $ordem['cliente'] }}</span></td>
                            <td class="px-5 py-3.5"><span class="text-muted text-xs">{{ $ordem['veiculo'] }}</span></td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 text-xs px-2 py-1 rounded-md font-medium"
                                      style="background: {{ $etapa['cor'] }}18; color: {{ $etapa['cor'] }};">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $etapa['cor'] }};"></span>
                                    {{ $etapa['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5"><span class="text-muted text-sm">{{ $ordem['mecanico'] }}</span></td>
                            <td class="px-5 py-3.5"><span class="font-mono text-sm text-void">R$ {{ number_format($ordem['total'], 0, ',', '.') }}</span></td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('oficina.os.show', $ordem['id']) }}"
                                   class="text-spark text-xs hover:underline font-medium">Ver →</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MOBILE: OS como cards --}}
    <div class="md:hidden bg-white rounded-xl" style="border: 1px solid var(--color-border);">
        <div class="flex items-center justify-between px-4 py-3.5" style="border-bottom: 1px solid var(--color-border);">
            <h2 class="font-display font-semibold text-void text-sm">OS Recentes</h2>
            <a href="{{ route('oficina.os.index') }}" class="text-spark text-xs font-medium hover:underline">Ver todas</a>
        </div>
        <div class="divide-y" style="border-color: var(--color-border);">
            @foreach($os as $ordem)
                @php $etapa = \App\Services\Mock\MockOsService::ETAPAS[$ordem['etapa_atual']]; @endphp
                <a href="{{ route('oficina.os.show', $ordem['id']) }}"
                   class="flex items-start justify-between gap-3 px-4 py-4 hover:bg-surface transition-colors block">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-xs text-muted whitespace-nowrap">{{ $ordem['id'] }}</span>
                            <span class="inline-flex items-center gap-1 text-xs px-1.5 py-0.5 rounded font-medium"
                                  style="background: {{ $etapa['cor'] }}18; color: {{ $etapa['cor'] }};">
                                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: {{ $etapa['cor'] }};"></span>
                                {{ $etapa['label'] }}
                            </span>
                        </div>
                        <p class="text-void font-semibold text-sm leading-snug">{{ $ordem['cliente'] }}</p>
                        <p class="text-muted text-xs mt-0.5 truncate">{{ $ordem['veiculo'] }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-mono text-sm font-bold text-void">R$ {{ number_format($ordem['total'], 0, ',', '.') }}</p>
                        <p class="text-spark text-xs mt-1">Ver →</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

</x-layouts.oficina>
