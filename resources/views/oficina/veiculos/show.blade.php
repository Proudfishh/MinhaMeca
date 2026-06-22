<x-layouts.oficina :title="$veiculo['marca'] . ' ' . $veiculo['modelo']">

<div class="space-y-5" x-data="{ tab: 'historico', editOpen: false }">

    {{-- ===== HEADER ===== --}}
    <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">

        {{-- Linha principal: ícone + info + botão --}}
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: rgba(30,58,95,0.08); border: 1.5px solid rgba(30,58,95,0.15);">
                <svg class="w-6 h-6" fill="none" stroke="#1E3A5F" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <h2 class="font-display font-bold text-void text-xl leading-tight">
                        {{ $veiculo['marca'] }} {{ $veiculo['modelo'] }}
                    </h2>
                    {{-- Editar só visível no desktop aqui --}}
                    <button @click="editOpen = true"
                            class="hidden md:flex flex-shrink-0 px-4 py-2 rounded-lg text-white text-sm font-semibold transition-opacity hover:opacity-90"
                            style="background: var(--color-spark);">
                        Editar
                    </button>
                </div>
                <div class="flex items-center flex-wrap gap-2 mt-1">
                    <span class="text-muted text-sm">{{ $veiculo['ano'] }} · {{ $veiculo['cor'] }}</span>
                    <span class="font-mono text-sm font-bold text-void px-2.5 py-0.5 rounded-md whitespace-nowrap"
                          style="background: rgba(15,23,42,0.06); border: 1px solid var(--color-border);">
                        {{ $veiculo['placa'] }}
                    </span>
                </div>
                <a href="{{ route('oficina.clientes.show', $veiculo['cliente_id']) }}"
                   class="text-spark text-sm font-medium hover:underline mt-1 inline-block">
                    {{ $cliente['nome'] ?? '—' }}
                </a>
            </div>
        </div>

        {{-- Editar mobile: botão full-width abaixo do header info --}}
        <button @click="editOpen = true"
                class="md:hidden w-full mt-4 py-2.5 rounded-lg text-white text-sm font-semibold transition-opacity hover:opacity-90"
                style="background: var(--color-spark);">
            Editar Veículo
        </button>

        {{-- STATS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-5 pt-5" style="border-top: 1px solid var(--color-border);">
            <div class="rounded-lg px-4 py-3" style="background: var(--color-surface); border: 1px solid var(--color-border);">
                <p class="text-muted text-xs mb-1">KM Atual</p>
                <p class="font-mono font-bold text-void text-base">{{ number_format($veiculo['km'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg px-4 py-3" style="background: var(--color-surface); border: 1px solid var(--color-border);">
                <p class="text-muted text-xs mb-1">Total de OS</p>
                <p class="font-mono font-bold text-void text-base">{{ count($osDoVeiculo) }}</p>
            </div>
            <div class="rounded-lg px-4 py-3" style="background: var(--color-surface); border: 1px solid var(--color-border);">
                <p class="text-muted text-xs mb-1">Total Gasto</p>
                <p class="font-mono font-bold text-void text-base">R$ {{ number_format($totalGasto, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg px-4 py-3" style="background: var(--color-surface); border: 1px solid var(--color-border);">
                <p class="text-muted text-xs mb-1">Última OS</p>
                <p class="font-mono font-bold text-void text-base">
                    @if($ultimaOS)
                        {{ \Carbon\Carbon::parse($ultimaOS['data_entrada'])->format('d/m/Y') }}
                    @else
                        —
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- ===== ABAS ===== --}}
    <div class="bg-white rounded-xl" style="border: 1px solid var(--color-border);">

        {{-- Tab nav --}}
        <div class="flex" style="border-bottom: 1px solid var(--color-border);">
            <button @click="tab = 'historico'"
                    :class="tab === 'historico' ? 'text-spark border-b-2 border-spark font-semibold' : 'text-muted hover:text-void'"
                    class="px-5 py-3.5 text-sm transition-colors -mb-px">
                Histórico
            </button>
            <button @click="tab = 'dados'"
                    :class="tab === 'dados' ? 'text-spark border-b-2 border-spark font-semibold' : 'text-muted hover:text-void'"
                    class="px-5 py-3.5 text-sm transition-colors -mb-px">
                Dados
            </button>
        </div>

        {{-- ABA HISTÓRICO --}}
        <div x-show="tab === 'historico'" x-transition>
            @if(count($osDoVeiculo) === 0)
                <div class="px-5 py-16 text-center">
                    <p class="text-muted text-sm">Nenhuma OS registrada para este veículo.</p>
                </div>
            @else

                {{-- MOBILE: cards (md:hidden) --}}
                <div class="md:hidden divide-y" style="border-color: var(--color-border);">
                    @foreach($osDoVeiculo as $os)
                        @php $etapa = $etapas[$os['etapa_atual']]; @endphp
                        <a href="{{ route('oficina.os.show', $os['id']) }}"
                           class="flex items-start justify-between gap-3 px-4 py-4 hover:bg-surface transition-colors block">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-mono text-xs text-muted">{{ $os['id'] }}</span>
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-md font-medium"
                                          style="background: {{ $etapa['cor'] }}18; color: {{ $etapa['cor'] }};">
                                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: {{ $etapa['cor'] }};"></span>
                                        {{ $etapa['label'] }}
                                    </span>
                                </div>
                                <p class="text-void text-sm font-medium leading-snug">{{ $os['descricao_cliente'] }}</p>
                                <p class="text-muted text-xs mt-0.5 font-mono">
                                    {{ \Carbon\Carbon::parse($os['data_entrada'])->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-mono text-sm font-bold text-void">R$ {{ number_format($os['total'], 0, ',', '.') }}</p>
                                <p class="text-spark text-xs mt-1">Ver →</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- DESKTOP: tabela (hidden md:block) --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">OS</th>
                                <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Entrada</th>
                                <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Serviço</th>
                                <th class="text-left px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Etapa</th>
                                <th class="text-right px-5 py-3 text-muted text-xs font-medium uppercase tracking-wide">Total</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($osDoVeiculo as $os)
                                @php $etapa = $etapas[$os['etapa_atual']]; @endphp
                                <tr class="hover:bg-surface transition-colors cursor-pointer"
                                    style="border-bottom: 1px solid var(--color-border);"
                                    onclick="window.location='{{ route('oficina.os.show', $os['id']) }}'">
                                    <td class="px-5 py-3.5">
                                        <span class="font-mono text-xs text-muted whitespace-nowrap">{{ $os['id'] }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="font-mono text-sm text-void">
                                            {{ \Carbon\Carbon::parse($os['data_entrada'])->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="text-void font-medium text-sm">{{ $os['descricao_cliente'] }}</p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-md font-medium"
                                              style="background: {{ $etapa['cor'] }}18; color: {{ $etapa['cor'] }};">
                                            <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $etapa['cor'] }};"></span>
                                            {{ $etapa['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="font-mono text-sm font-semibold text-void">
                                            R$ {{ number_format($os['total'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-spark text-xs font-medium whitespace-nowrap">Ver →</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endif
        </div>

        {{-- ABA DADOS --}}
        <div x-show="tab === 'dados'" x-transition>
            <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid var(--color-border);">
                <p class="text-muted text-xs">Dados de cadastro do veículo.</p>
                <button @click="editOpen = true"
                        class="text-spark text-sm font-semibold hover:underline">
                    Editar
                </button>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-0">
                @php
                    $campos = [
                        ['label' => 'Marca',       'valor' => $veiculo['marca'],       'mono' => false],
                        ['label' => 'Modelo',      'valor' => $veiculo['modelo'],      'mono' => false],
                        ['label' => 'Ano',         'valor' => $veiculo['ano'],         'mono' => false],
                        ['label' => 'Cor',         'valor' => $veiculo['cor'],         'mono' => false],
                        ['label' => 'Placa',       'valor' => $veiculo['placa'],       'mono' => true],
                        ['label' => 'Chassi',      'valor' => $veiculo['chassi'],      'mono' => true,  'break' => true],
                        ['label' => 'Combustível', 'valor' => $veiculo['combustivel'], 'mono' => false],
                        ['label' => 'Câmbio',      'valor' => $veiculo['cambio'],      'mono' => false],
                        ['label' => 'KM Atual',    'valor' => number_format($veiculo['km'], 0, ',', '.'), 'mono' => true],
                    ];
                @endphp
                @foreach($campos as $campo)
                    <div class="px-4 py-4 overflow-hidden" style="border-bottom: 1px solid var(--color-border); border-right: 1px solid var(--color-border);">
                        <p class="text-muted text-xs uppercase tracking-wide mb-1">{{ $campo['label'] }}</p>
                        <p class="{{ $campo['mono'] ? 'font-mono' : '' }} {{ ($campo['break'] ?? false) ? 'break-all' : '' }} text-void text-sm font-medium">{{ $campo['valor'] }}</p>
                    </div>
                @endforeach
                <div class="px-4 py-4" style="border-bottom: 1px solid var(--color-border); border-right: 1px solid var(--color-border);">
                    <p class="text-muted text-xs uppercase tracking-wide mb-1">Proprietário</p>
                    <a href="{{ route('oficina.clientes.show', $veiculo['cliente_id']) }}"
                       class="text-spark text-sm font-medium hover:underline">
                        {{ $cliente['nome'] ?? '—' }}
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== MODAL EDITAR ===== --}}
    <div x-show="editOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
         style="background: rgba(15,23,42,0.5);"
         @click.self="editOpen = false"
         @keydown.escape.window="editOpen = false">

        <div x-show="editOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 md:translate-y-0 md:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 md:translate-y-0 md:scale-95"
             class="bg-white w-full md:max-w-lg md:rounded-xl rounded-t-2xl shadow-xl"
             style="border: 1px solid var(--color-border);">

            {{-- Handle mobile --}}
            <div class="md:hidden flex justify-center pt-3 pb-1">
                <div class="w-10 h-1 rounded-full" style="background: var(--color-border);"></div>
            </div>

            {{-- Modal header --}}
            <div class="flex items-center justify-between px-6 py-4" style="border-bottom: 1px solid var(--color-border);">
                <h3 class="font-display font-bold text-void text-lg">Editar Veículo</h3>
                <button @click="editOpen = false" class="text-muted hover:text-void transition-colors p-1 rounded-lg hover:bg-surface">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal body --}}
            <div class="px-6 py-5 space-y-4">
                {{-- 1 coluna no mobile, 2 colunas no desktop --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">KM Atual</label>
                        <input type="number"
                               value="{{ $veiculo['km'] }}"
                               placeholder="ex: 87400"
                               class="w-full px-3 py-2.5 rounded-lg text-sm font-mono text-void focus:outline-none focus:ring-2"
                               style="border: 1px solid var(--color-border); background: var(--color-surface);">
                    </div>
                    <div>
                        <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">Cor</label>
                        <input type="text"
                               value="{{ $veiculo['cor'] }}"
                               placeholder="ex: Prata"
                               class="w-full px-3 py-2.5 rounded-lg text-sm text-void focus:outline-none focus:ring-2"
                               style="border: 1px solid var(--color-border); background: var(--color-surface);">
                    </div>
                    <div>
                        <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">Placa</label>
                        <input type="text"
                               value="{{ $veiculo['placa'] }}"
                               placeholder="ex: ABC-1234"
                               class="w-full px-3 py-2.5 rounded-lg text-sm font-mono text-void focus:outline-none focus:ring-2 uppercase"
                               style="border: 1px solid var(--color-border); background: var(--color-surface);">
                    </div>
                    <div>
                        <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">Chassi</label>
                        <input type="text"
                               value="{{ $veiculo['chassi'] }}"
                               placeholder="ex: 9BW..."
                               class="w-full px-3 py-2.5 rounded-lg text-sm font-mono text-void focus:outline-none focus:ring-2"
                               style="border: 1px solid var(--color-border); background: var(--color-surface);">
                    </div>
                    <div>
                        <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">Combustível</label>
                        <select class="w-full px-3 py-2.5 rounded-lg text-sm text-void focus:outline-none focus:ring-2"
                                style="border: 1px solid var(--color-border); background: var(--color-surface);">
                            @foreach(['Flex', 'Gasolina', 'Diesel', 'Elétrico', 'Híbrido'] as $opt)
                                <option {{ $veiculo['combustivel'] === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-muted uppercase tracking-wide mb-1.5">Câmbio</label>
                        <select class="w-full px-3 py-2.5 rounded-lg text-sm text-void focus:outline-none focus:ring-2"
                                style="border: 1px solid var(--color-border); background: var(--color-surface);">
                            @foreach(['Manual', 'Automático', 'CVT'] as $opt)
                                <option {{ $veiculo['cambio'] === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <p class="text-muted text-xs">Marca, modelo e ano não são editáveis aqui.</p>
            </div>

            {{-- Modal footer --}}
            <div class="flex gap-3 px-6 py-4" style="border-top: 1px solid var(--color-border);">
                <button @click="editOpen = false"
                        class="flex-1 md:flex-none md:px-4 py-2.5 rounded-lg text-sm text-muted hover:text-void hover:bg-surface transition-colors"
                        style="border: 1px solid var(--color-border);">
                    Cancelar
                </button>
                <button @click="alert('Fase 1 — funcionalidade mockada'); editOpen = false"
                        class="flex-1 md:flex-none md:px-4 py-2.5 rounded-lg text-white text-sm font-semibold transition-opacity hover:opacity-90"
                        style="background: var(--color-spark);">
                    Salvar
                </button>
            </div>
        </div>
    </div>

</div>

</x-layouts.oficina>
