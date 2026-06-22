<x-layouts.oficina title="Ordens de Serviço">

<div x-data="{ view: 'kanban', grupos: @js(array_fill_keys(array_keys($etapas), false)) }">

    {{-- ===== HEADER DESKTOP ===== --}}
    <div class="hidden md:flex items-center justify-between mb-5">
        <div>
            <p class="text-muted text-xs mb-0.5">{{ collect($todasOs)->count() }} ordens de serviço</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Toggle Kanban / Tabela --}}
            <div class="flex rounded-lg p-0.5" style="background: var(--color-border);">
                <button
                    @click="view = 'kanban'"
                    :class="view === 'kanban' ? 'bg-white text-void shadow-sm' : 'text-muted hover:text-void'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                    Kanban
                </button>
                <button
                    @click="view = 'tabela'"
                    :class="view === 'tabela' ? 'bg-white text-void shadow-sm' : 'text-muted hover:text-void'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Tabela
                </button>
            </div>

            <a href="{{ route('oficina.os.create') }}"
               class="flex items-center gap-2 bg-spark hover:bg-blue-500 text-white text-xs font-medium px-4 py-2 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nova OS
            </a>
        </div>
    </div>

    {{-- ===== HEADER MOBILE ===== --}}
    <div class="md:hidden flex items-center justify-between mb-4">
        <div>
            <p class="text-muted text-xs mb-0.5">{{ collect($todasOs)->count() }} ordens de serviço</p>
            <h2 class="font-display font-bold text-void text-base leading-tight">Ordens de Serviço</h2>
        </div>
        <a href="{{ route('oficina.os.create') }}"
           class="flex items-center justify-center w-9 h-9 bg-spark hover:bg-blue-500 text-white rounded-xl transition-colors shadow-sm"
           style="box-shadow: 0 2px 8px rgba(59,130,246,.35);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
        </a>
    </div>

    {{-- ===== LISTA MOBILE (agrupada por etapa) ===== --}}
    <div class="md:hidden flex flex-col gap-3">
        @foreach($etapas as $key => $etapa)
            @php $cards = $filaEtapas[$key] ?? []; @endphp

            <div class="rounded-xl overflow-hidden" style="border: 1px solid var(--color-border);">

                {{-- Cabeçalho do grupo (clicável para colapsar) --}}
                <button @click="grupos.{{ $key }} = !grupos.{{ $key }}"
                        class="w-full flex items-center justify-between px-4 py-3 transition-colors"
                        style="background: {{ $etapa['cor'] }}0f;">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                             style="background: {{ $etapa['cor'] }};"></div>
                        <span class="text-xs font-bold tracking-wide"
                              style="color: {{ $etapa['cor'] }};">{{ strtoupper($etapa['label']) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-full"
                              style="background: {{ $etapa['cor'] }}22; color: {{ $etapa['cor'] }};">
                            {{ count($cards) }}
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-200"
                             :class="grupos.{{ $key }} ? 'rotate-0' : '-rotate-90'"
                             fill="none" stroke="{{ $etapa['cor'] }}" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                {{-- Cards do grupo --}}
                <div x-show="grupos.{{ $key }}"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="flex flex-col divide-y bg-white"
                     style="border-top: 1px solid var(--color-border);">

                    @forelse($cards as $os)
                        <a href="{{ route('oficina.os.show', $os['id']) }}"
                           class="flex items-start gap-3 px-4 py-3 hover:bg-surface active:bg-surface transition-colors">
                            {{-- Barra colorida da etapa --}}
                            <div class="w-1 self-stretch rounded-full flex-shrink-0 mt-0.5"
                                 style="background: {{ $etapa['cor'] }};"></div>
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-mono text-[10px] text-muted mb-0.5">{{ $os['id'] }}</p>
                                <p class="font-bold text-void text-sm leading-tight">{{ $os['cliente'] }}</p>
                                @php
                                    $partes = explode(' · ', $os['veiculo']);
                                    $modeloAno = $partes[0] ?? '';
                                    $placa = $partes[2] ?? '';
                                @endphp
                                <p class="text-muted text-xs mt-0.5">{{ $modeloAno }}@if($placa) · <span class="font-mono">{{ $placa }}</span>@endif</p>
                                {{-- Footer do card --}}
                                <div class="flex items-center gap-1.5 mt-2">
                                    <div class="w-5 h-5 rounded-full bg-ocean flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-[9px] font-bold">{{ substr($os['mecanico'], 0, 1) }}</span>
                                    </div>
                                    <span class="text-muted text-[11px]">{{ explode(' ', $os['mecanico'])[0] }}</span>
                                </div>
                            </div>
                            {{-- Data previsão --}}
                            <div class="flex-shrink-0 flex flex-col items-end justify-between self-stretch">
                                @if($os['previsao_entrega'])
                                    <span class="font-mono text-[11px] text-muted">
                                        {{ \Carbon\Carbon::parse($os['previsao_entrega'])->format('d/m') }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-muted">—</span>
                                @endif
                                <svg class="w-4 h-4 text-muted opacity-40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-4 flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full opacity-30" style="background: {{ $etapa['cor'] }};"></div>
                            <p class="text-muted text-xs">Nenhuma OS nesta etapa</p>
                        </div>
                    @endforelse

                </div>
            </div>
        @endforeach
    </div>

    {{-- ============================= DESKTOP: KANBAN + TABELA ============================= --}}
    <div class="hidden md:block">

    {{-- ============================= KANBAN ============================= --}}
    <div x-show="view === 'kanban'"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="flex gap-3 overflow-x-auto pb-4"
         style="min-height: calc(100vh - 180px);">

        @foreach($etapas as $key => $etapa)
            @php $cards = $filaEtapas[$key] ?? []; @endphp

            <div class="flex-shrink-0 w-64 flex flex-col rounded-xl"
                 style="background: rgba(0,0,0,0.03); border: 1px solid var(--color-border);">

                {{-- Cabeçalho da coluna --}}
                <div class="flex items-center justify-between px-3 py-3 flex-shrink-0"
                     style="border-bottom: 1px solid var(--color-border);">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full flex-shrink-0"
                             style="background: {{ $etapa['cor'] }};"></div>
                        <span class="text-xs font-semibold text-void">{{ $etapa['label'] }}</span>
                    </div>
                    <span class="font-mono text-[10px] font-bold px-1.5 py-0.5 rounded"
                          style="background: {{ $etapa['cor'] }}18; color: {{ $etapa['cor'] }};">
                        {{ count($cards) }}
                    </span>
                </div>

                {{-- Cards --}}
                <div class="flex-1 p-2 space-y-2 overflow-y-auto">

                    @forelse($cards as $os)
                        <a href="{{ route('oficina.os.show', $os['id']) }}"
                           class="block bg-white rounded-lg p-3 hover:shadow-md transition-all group"
                           style="border: 1px solid var(--color-border); border-left: 3px solid {{ $etapa['cor'] }};">

                            {{-- OS ID --}}
                            <p class="font-mono text-[10px] text-muted mb-1.5">{{ $os['id'] }}</p>

                            {{-- Cliente --}}
                            <p class="font-semibold text-void text-sm leading-tight mb-1">
                                {{ $os['cliente'] }}
                            </p>

                            {{-- Veículo --}}
                            @php
                                $partes = explode(' · ', $os['veiculo']);
                                $modeloAno = $partes[0] ?? '';
                                $placa = $partes[2] ?? '';
                            @endphp
                            <p class="text-muted text-xs truncate">{{ $modeloAno }}</p>

                            @if($placa)
                                <span class="inline-block font-mono text-[10px] text-void px-1.5 py-0.5 rounded mt-1"
                                      style="background: var(--color-surface); border: 1px solid var(--color-border);">
                                    {{ $placa }}
                                </span>
                            @endif

                            {{-- Footer do card --}}
                            <div class="flex items-center justify-between mt-3 pt-2.5"
                                 style="border-top: 1px solid var(--color-border);">
                                {{-- Mecânico --}}
                                <div class="flex items-center gap-1.5">
                                    <div class="w-5 h-5 rounded-full bg-ocean flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-[9px] font-bold">
                                            {{ substr($os['mecanico'], 0, 1) }}
                                        </span>
                                    </div>
                                    <span class="text-muted text-[10px] truncate max-w-[80px]">
                                        {{ explode(' ', $os['mecanico'])[0] }}
                                    </span>
                                </div>

                                {{-- Data --}}
                                @if($os['previsao_entrega'])
                                    <span class="text-[10px] text-muted font-mono">
                                        {{ \Carbon\Carbon::parse($os['previsao_entrega'])->format('d/m') }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-muted">—</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="w-8 h-8 rounded-full mb-2 flex items-center justify-center"
                                 style="background: {{ $etapa['cor'] }}18;">
                                <div class="w-3 h-3 rounded-full" style="background: {{ $etapa['cor'] }}; opacity: 0.4;"></div>
                            </div>
                            <p class="text-muted text-xs">Nenhuma OS</p>
                        </div>
                    @endforelse

                </div>
            </div>
        @endforeach

    </div>

    {{-- ============================= TABELA ============================= --}}
    <div x-show="view === 'tabela'"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="bg-white rounded-xl overflow-hidden"
         style="border: 1px solid var(--color-border);">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--color-border); background: var(--color-surface);">
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">OS</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Cliente</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Veículo</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Etapa</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Mecânico</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Entrada</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Previsão</th>
                        <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Total</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todasOs as $os)
                        @php $etapa = $etapas[$os['etapa_atual']]; @endphp
                        <tr class="hover:bg-surface transition-colors" style="border-bottom: 1px solid var(--color-border);">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs text-muted">{{ $os['id'] }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-void font-medium">{{ $os['cliente'] }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @php $partes = explode(' · ', $os['veiculo']); @endphp
                                <div>
                                    <p class="text-muted text-xs">{{ $partes[0] ?? '' }}</p>
                                    @isset($partes[2])
                                        <span class="font-mono text-[10px] text-void">{{ $partes[2] }}</span>
                                    @endisset
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 text-xs px-2 py-1 rounded-md font-medium"
                                      style="background: {{ $etapa['cor'] }}18; color: {{ $etapa['cor'] }};">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $etapa['cor'] }};"></span>
                                    {{ $etapa['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-ocean flex items-center justify-center">
                                        <span class="text-white text-[9px] font-bold">{{ substr($os['mecanico'], 0, 1) }}</span>
                                    </div>
                                    <span class="text-muted text-sm">{{ $os['mecanico'] }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs text-muted">
                                    {{ \Carbon\Carbon::parse($os['data_entrada'])->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($os['previsao_entrega'])
                                    <span class="font-mono text-xs text-muted">
                                        {{ \Carbon\Carbon::parse($os['previsao_entrega'])->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-sm text-void">
                                    R$ {{ number_format($os['total'], 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('oficina.os.show', $os['id']) }}"
                                   class="text-spark text-xs hover:underline font-medium">Ver →</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    </div> {{-- /DESKTOP: KANBAN + TABELA --}}

</div>

</x-layouts.oficina>
