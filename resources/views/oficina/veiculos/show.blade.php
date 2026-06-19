<x-layouts.oficina :title="$veiculo['marca'] . ' ' . $veiculo['modelo']">

<div class="space-y-5">

    {{-- ===== HEADER DO VEÍCULO ===== --}}
    <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(30,58,95,0.08); border: 1.5px solid rgba(30,58,95,0.15);">
                    <svg class="w-6 h-6 text-ocean" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-display font-bold text-void text-xl leading-tight">
                        {{ $veiculo['marca'] }} {{ $veiculo['modelo'] }}
                    </h2>
                    <p class="text-muted text-sm mt-0.5">
                        {{ $veiculo['ano'] }} · {{ $veiculo['cor'] }}
                    </p>
                </div>
            </div>
            <span class="font-mono text-sm font-bold text-void px-3 py-1.5 rounded-lg"
                  style="background: rgba(15,23,42,0.06); border: 1px solid var(--color-border);">
                {{ $veiculo['placa'] }}
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-5 pt-5" style="border-top: 1px solid var(--color-border);">
            <div>
                <p class="text-muted text-xs mb-0.5">Proprietário</p>
                <a href="{{ route('oficina.clientes.show', $veiculo['cliente_id']) }}"
                   class="text-spark text-sm font-medium hover:underline">
                    {{ $cliente['nome'] ?? '—' }}
                </a>
            </div>
            <div>
                <p class="text-muted text-xs mb-0.5">Total de OS</p>
                <p class="font-display font-bold text-void text-sm">{{ count($osDoVeiculo) }}</p>
            </div>
            <div>
                <p class="text-muted text-xs mb-0.5">Última entrada</p>
                <p class="font-mono text-sm text-void">
                    @if(count($osDoVeiculo) > 0)
                        {{ \Carbon\Carbon::parse($osDoVeiculo[0]['data_entrada'])->format('d/m/Y') }}
                    @else
                        —
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- ===== HISTÓRICO DE OS ===== --}}
    <div class="bg-white rounded-xl" style="border: 1px solid var(--color-border);">
        <div class="px-5 py-4" style="border-bottom: 1px solid var(--color-border);">
            <h3 class="font-display font-semibold text-void text-base">Histórico de Ordens de Serviço</h3>
        </div>

        @if(count($osDoVeiculo) === 0)
            <div class="px-5 py-16 text-center">
                <p class="text-muted text-sm">Nenhuma OS registrada para este veículo.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--color-border);">
                            <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">OS</th>
                            <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Entrada</th>
                            <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Etapa</th>
                            <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Mecânico</th>
                            <th class="text-right px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Total</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($osDoVeiculo as $os)
                            @php $etapa = $etapas[$os['etapa_atual']]; @endphp
                            <tr class="hover:bg-surface transition-colors" style="border-bottom: 1px solid var(--color-border);">
                                <td class="px-5 py-3.5">
                                    <span class="font-mono text-xs text-muted">{{ $os['id'] }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-mono text-sm text-void">
                                        {{ \Carbon\Carbon::parse($os['data_entrada'])->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-md font-medium"
                                          style="background: {{ $etapa['cor'] }}18; color: {{ $etapa['cor'] }};">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $etapa['cor'] }};"></span>
                                        {{ $etapa['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-muted text-sm">{{ $os['mecanico'] }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-mono text-sm text-void">
                                        R$ {{ number_format($os['total'], 2, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('oficina.os.show', $os['id']) }}"
                                       class="text-spark text-xs font-medium hover:underline">Ver →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

</x-layouts.oficina>
