<x-layouts.oficina title="Dashboard">

    {{-- ==================== MÉTRICAS ==================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
            $cards = [
                ['label' => 'OS Abertas',         'valor' => $metricas['abertas'],          'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'cor' => '#3B82F6', 'bg' => 'rgba(59,130,246,0.08)'],
                ['label' => 'Em Andamento',        'valor' => $metricas['em_andamento'],     'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                    'cor' => '#7C3AED', 'bg' => 'rgba(124,58,237,0.08)'],
                ['label' => 'Finalizadas Hoje',   'valor' => $metricas['finalizadas_hoje'],  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                  'cor' => '#10B981', 'bg' => 'rgba(16,185,129,0.08)'],
                ['label' => 'Receita do Mês',     'valor' => 'R$ '.number_format($metricas['receita_mes'], 0, ',', '.'), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'cor' => '#F59E0B', 'bg' => 'rgba(245,158,11,0.08)'],
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- ==================== FILA POR ETAPA ==================== --}}
        <div class="lg:col-span-2 bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
            <h2 class="font-display font-semibold text-void text-sm mb-4">Fila por Etapa</h2>
            <div class="space-y-2.5">
                @foreach(\App\Services\Mock\MockOsService::ETAPAS as $key => $etapa)
                    @php $count = count($filaEtapas[$key] ?? []); $max = 5; @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $etapa['cor'] }};"></div>
                        <span class="text-xs text-muted w-36 truncate">{{ $etapa['label'] }}</span>
                        <div class="flex-1 h-1.5 rounded-full bg-border overflow-hidden">
                            <div class="h-full rounded-full transition-all"
                                 style="width: {{ $count > 0 ? ($count / $max * 100) : 0 }}%; background: {{ $etapa['cor'] }}; opacity: 0.7;"></div>
                        </div>
                        <span class="font-mono text-xs font-medium text-void w-4 text-right">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ==================== ESTOQUE BAIXO ==================== --}}
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
                            <span class="font-mono text-xs flex-shrink-0"
                                  style="color: #B45309;">{{ $peca['quantidade'] }} un</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ==================== ÚLTIMAS OS ==================== --}}
    <div class="bg-white rounded-xl" style="border: 1px solid var(--color-border);">
        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid var(--color-border);">
            <h2 class="font-display font-semibold text-void text-sm">Ordens de Serviço Recentes</h2>
            <a href="{{ route('oficina.os.index') }}"
               class="text-spark text-xs font-medium hover:underline">Ver todas</a>
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
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs text-muted">{{ $ordem['id'] }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-void font-medium text-sm">{{ $ordem['cliente'] }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-muted text-xs">{{ $ordem['veiculo'] }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 text-xs px-2 py-1 rounded-md font-medium"
                                      style="background: {{ $etapa['cor'] }}18; color: {{ $etapa['cor'] }};">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $etapa['cor'] }};"></span>
                                    {{ $etapa['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-muted text-sm">{{ $ordem['mecanico'] }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-sm text-void">R$ {{ number_format($ordem['total'], 2, ',', '.') }}</span>
                            </td>
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

</x-layouts.oficina>
