<x-layouts.oficina :title="$os['id']">
@php
    $etapaKeys   = array_keys($etapas);
    $etapaAtual  = $os['etapa_atual'];
    $idxAtual    = array_search($etapaAtual, $etapaKeys);
    $etapaInfo   = $etapas[$etapaAtual];
    $proximaKey  = $etapaKeys[$idxAtual + 1] ?? null;
    $anteriorKey = $idxAtual > 0 ? $etapaKeys[$idxAtual - 1] : null;
@endphp

<div x-data="{ moverModal: false, etapaDestino: '' }">

{{-- ===== HEADER ===== --}}
<div class="bg-white rounded-xl p-5 mb-4" style="border: 1px solid var(--color-border);">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

        {{-- Info principal --}}
        <div class="flex items-start gap-4">
            {{-- Ícone veículo --}}
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: {{ $etapaInfo['cor'] }}15; border: 1.5px solid {{ $etapaInfo['cor'] }}30;">
                <svg class="w-6 h-6" fill="none" stroke="{{ $etapaInfo['cor'] }}" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="font-mono text-xs text-muted">{{ $os['id'] }}</span>
                    <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-md font-medium"
                          style="background: {{ $etapaInfo['cor'] }}18; color: {{ $etapaInfo['cor'] }};">
                        <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $etapaInfo['cor'] }};"></span>
                        {{ $etapaInfo['label'] }}
                    </span>
                </div>
                <h2 class="font-display font-bold text-void text-lg leading-tight">{{ $os['cliente'] }}</h2>
                <p class="text-muted text-sm">{{ $os['veiculo'] }}</p>
            </div>
        </div>

        {{-- Meta --}}
        <div class="flex items-center gap-6 text-sm">
            <div>
                <p class="text-muted text-xs mb-0.5">Mecânico</p>
                <div class="flex items-center gap-1.5">
                    <div class="w-5 h-5 rounded-full bg-ocean flex items-center justify-center">
                        <span class="text-white text-[9px] font-bold">{{ substr($os['mecanico'], 0, 1) }}</span>
                    </div>
                    <span class="text-void font-medium text-sm">{{ $os['mecanico'] }}</span>
                </div>
            </div>
            <div>
                <p class="text-muted text-xs mb-0.5">Entrada</p>
                <span class="font-mono text-sm text-void">
                    {{ \Carbon\Carbon::parse($os['data_entrada'])->format('d/m/Y') }}
                </span>
            </div>
            @if($os['previsao_entrega'])
            <div>
                <p class="text-muted text-xs mb-0.5">Previsão</p>
                <span class="font-mono text-sm text-void">
                    {{ \Carbon\Carbon::parse($os['previsao_entrega'])->format('d/m/Y') }}
                </span>
            </div>
            @endif
            <div>
                <p class="text-muted text-xs mb-0.5">Total</p>
                <span class="font-display font-bold text-void">
                    R$ {{ number_format($os['total'], 2, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ===== STEPPER HORIZONTAL ===== --}}
<div class="bg-white rounded-xl px-5 py-4 mb-4" style="border: 1px solid var(--color-border);">
    <div class="flex items-center">
        @foreach($etapas as $key => $etapa)
            @php
                $idx       = array_search($key, $etapaKeys);
                $concluida = $idx < $idxAtual;
                $atual     = $key === $etapaAtual;
                $futura    = $idx > $idxAtual;
            @endphp

            {{-- Etapa --}}
            <div class="flex-1 flex flex-col items-center relative group cursor-default">
                {{-- Linha conectora (antes) --}}
                @if($idx > 0)
                    <div class="absolute left-0 top-4 w-1/2 h-px -translate-y-1/2"
                         style="background: {{ $concluida || $atual ? $etapa['cor'] : 'var(--color-border)' }}; opacity: {{ $concluida ? '0.5' : ($atual ? '1' : '1') }};"></div>
                @endif
                {{-- Linha conectora (depois) --}}
                @if($idx < count($etapas) - 1)
                    <div class="absolute right-0 top-4 w-1/2 h-px -translate-y-1/2"
                         style="background: {{ $concluida ? $etapa['cor'] : 'var(--color-border)' }}; opacity: 0.4;"></div>
                @endif

                {{-- Círculo --}}
                <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center transition-all"
                     style="
                        background: {{ $atual ? $etapa['cor'] : ($concluida ? $etapa['cor'].'22' : 'var(--color-surface)') }};
                        border: 2px solid {{ $atual || $concluida ? $etapa['cor'] : 'var(--color-border)' }};
                     ">
                    @if($concluida)
                        <svg class="w-3.5 h-3.5" fill="none" stroke="{{ $etapa['cor'] }}" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    @elseif($atual)
                        <div class="w-2.5 h-2.5 rounded-full bg-white"></div>
                    @else
                        <div class="w-2 h-2 rounded-full" style="background: var(--color-border);"></div>
                    @endif
                </div>

                {{-- Label --}}
                <span class="mt-1.5 text-[10px] font-medium text-center leading-tight px-1"
                      style="color: {{ $atual ? $etapa['cor'] : ($concluida ? 'var(--color-muted)' : 'var(--color-border)') }};">
                    {{ $etapa['label'] }}
                </span>
            </div>
        @endforeach
    </div>

    {{-- Ações de transição --}}
    <div class="flex items-center justify-between mt-4 pt-3" style="border-top: 1px solid var(--color-border);">
        <div class="flex items-center gap-2">
            @if($anteriorKey)
                <button onclick="alert('Fase 1 — mockado')"
                        class="flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg text-muted hover:text-void transition-colors"
                        style="border: 1px solid var(--color-border);">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Regredir para {{ $etapas[$anteriorKey]['label'] }}
                </button>
            @endif
        </div>

        <div class="flex items-center gap-2">
            <button @click="moverModal = true"
                    class="text-xs px-3 py-1.5 rounded-lg text-muted hover:text-void transition-colors"
                    style="border: 1px solid var(--color-border);">
                Mover para...
            </button>

            @if($proximaKey)
                <button onclick="alert('Fase 1 — mockado')"
                        class="flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg text-white transition-colors"
                        style="background: {{ $etapaInfo['cor'] }};">
                    Avançar para {{ $etapas[$proximaKey]['label'] }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @else
                <span class="text-xs text-muted px-3 py-1.5">Etapa final</span>
            @endif
        </div>
    </div>
</div>

{{-- ===== CONTEÚDO PRINCIPAL ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Coluna principal: etapa atual --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- ---- CHECK-IN ---- --}}
        @if($etapaAtual === 'checkin')
        <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
            <h3 class="font-display font-semibold text-void text-sm mb-4">Checklist de Entrada</h3>
            <div class="grid grid-cols-2 gap-2 mb-4">
                @foreach($os['etapa_checkin']['checklist'] as $item)
                    <label class="flex items-center gap-2 p-2.5 rounded-lg cursor-pointer group"
                           style="border: 1px solid var(--color-border); background: var(--color-surface);">
                        <div class="w-4 h-4 rounded flex items-center justify-center flex-shrink-0"
                             style="background: #10B98118; border: 1.5px solid #10B981;">
                            <svg class="w-2.5 h-2.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm text-void">{{ $item }}</span>
                    </label>
                @endforeach
                @if(empty($os['etapa_checkin']['checklist']))
                    <p class="text-muted text-sm col-span-2">Checklist não preenchido ainda.</p>
                @endif
            </div>
            @if($os['etapa_checkin']['observacao'])
                <div class="p-3 rounded-lg" style="background: var(--color-surface); border: 1px solid var(--color-border);">
                    <p class="text-xs text-muted mb-1">Observações</p>
                    <p class="text-sm text-void">{{ $os['etapa_checkin']['observacao'] }}</p>
                </div>
            @endif
        </div>
        @endif

        {{-- ---- DIAGNÓSTICO ---- --}}
        @if($etapaAtual === 'diagnostico')
        <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
            <h3 class="font-display font-semibold text-void text-sm mb-4">Diagnóstico do Problema</h3>
            @if($os['etapa_diagnostico']['descricao'])
                <div class="p-4 rounded-lg leading-relaxed text-sm text-void"
                     style="background: var(--color-surface); border: 1px solid var(--color-border);">
                    {{ $os['etapa_diagnostico']['descricao'] }}
                </div>
            @else
                <p class="text-muted text-sm">Diagnóstico ainda não registrado.</p>
            @endif
        </div>
        @endif

        {{-- ---- AGUARDANDO PEÇAS ---- --}}
        @if($etapaAtual === 'pecas')
        <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display font-semibold text-void text-sm">Orçamento de Peças</h3>
                @if($os['etapa_pecas']['aprovado'])
                    <span class="text-xs px-2 py-0.5 rounded-md font-medium"
                          style="background: #10B98115; color: #059669;">
                        ✓ Aprovado em {{ \Carbon\Carbon::parse($os['etapa_pecas']['aprovado_em'])->format('d/m H:i') }}
                    </span>
                @else
                    <span class="text-xs px-2 py-0.5 rounded-md font-medium"
                          style="background: #F59E0B15; color: #B45309;">
                        Aguardando aprovação
                    </span>
                @endif
            </div>
            <table class="w-full text-sm mb-4">
                <thead>
                    <tr style="border-bottom: 1px solid var(--color-border);">
                        <th class="text-left text-xs text-muted font-medium pb-2">Peça</th>
                        <th class="text-left text-xs text-muted font-medium pb-2">Origem</th>
                        <th class="text-center text-xs text-muted font-medium pb-2">Qtd</th>
                        <th class="text-right text-xs text-muted font-medium pb-2">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($os['etapa_pecas']['itens'] as $item)
                    <tr style="border-bottom: 1px solid var(--color-border);">
                        <td class="py-2.5 text-void">{{ $item['descricao'] }}</td>
                        <td class="py-2.5">
                            <span class="text-xs px-1.5 py-0.5 rounded font-medium"
                                  style="{{ $item['origem'] === 'estoque' ? 'background:#3B82F615;color:#1D4ED8' : 'background:#7C3AED15;color:#6D28D9' }}">
                                {{ $item['origem'] === 'estoque' ? 'Estoque' : 'Externo' }}
                            </span>
                        </td>
                        <td class="py-2.5 text-center font-mono text-muted text-xs">{{ $item['qtd'] }}x</td>
                        <td class="py-2.5 text-right font-mono text-void">R$ {{ number_format($item['valor'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="pt-3 text-right text-xs text-muted font-medium">Total peças</td>
                        <td class="pt-3 text-right font-mono font-bold text-void">
                            R$ {{ number_format(collect($os['etapa_pecas']['itens'])->sum('valor'), 2, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            @if(!$os['etapa_pecas']['aprovado'])
                <button onclick="alert('Fase 1 — mockado')"
                        class="w-full py-2.5 text-sm font-medium text-white rounded-lg transition-colors"
                        style="background: #10B981;">
                    Registrar aprovação do cliente
                </button>
            @endif
        </div>
        @endif

        {{-- ---- SERVIÇO ---- --}}
        @if($etapaAtual === 'servico')
        <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
            <h3 class="font-display font-semibold text-void text-sm mb-4">Logs de Progresso</h3>
            @if(!empty($os['etapa_servico']['logs']))
                <div class="space-y-3">
                    @foreach($os['etapa_servico']['logs'] as $log)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-2 h-2 rounded-full mt-1 flex-shrink-0" style="background: #7C3AED;"></div>
                                @if(!$loop->last)
                                    <div class="w-px flex-1 mt-1" style="background: var(--color-border);"></div>
                                @endif
                            </div>
                            <div class="pb-3">
                                <span class="font-mono text-[10px] text-muted block mb-0.5">
                                    {{ \Carbon\Carbon::parse($log['hora'])->format('d/m H:i') }}
                                </span>
                                <p class="text-sm text-void">{{ $log['descricao'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-sm">Nenhum log de progresso registrado.</p>
            @endif
            <button onclick="alert('Fase 1 — mockado')"
                    class="mt-4 flex items-center gap-2 text-xs px-3 py-2 rounded-lg text-muted hover:text-void transition-colors"
                    style="border: 1px solid var(--color-border);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar atualização
            </button>
        </div>
        @endif

        {{-- ---- TESTES ---- --}}
        @if($etapaAtual === 'testes')
        <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
            <h3 class="font-display font-semibold text-void text-sm mb-4">Resultado dos Testes</h3>
            <div class="space-y-3">
                @foreach(['Teste de frenagem', 'Verificação de fluidos', 'Teste de suspensão', 'Sistema elétrico'] as $teste)
                    <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer"
                           style="border: 1px solid var(--color-border); background: var(--color-surface);">
                        <div class="w-4 h-4 rounded flex-shrink-0"
                             style="border: 1.5px solid var(--color-border); background: white;"></div>
                        <span class="text-sm text-void">{{ $teste }}</span>
                    </label>
                @endforeach
            </div>
            <button onclick="alert('Fase 1 — mockado')"
                    class="mt-4 w-full py-2.5 text-sm font-medium text-white rounded-lg"
                    style="background: #06B6D4;">
                Salvar resultados dos testes
            </button>
        </div>
        @endif

        {{-- ---- FINALIZAÇÃO ---- --}}
        @if($etapaAtual === 'finalizacao')
        <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
            <h3 class="font-display font-semibold text-void text-sm mb-4">Checklist de Saída</h3>
            @if(!empty($os['etapa_finalizacao']['checklist_saida']))
                <div class="space-y-2 mb-4">
                    @foreach($os['etapa_finalizacao']['checklist_saida'] as $item)
                        <label class="flex items-center gap-2 p-2.5 rounded-lg"
                               style="background: var(--color-surface); border: 1px solid var(--color-border);">
                            <div class="w-4 h-4 rounded flex items-center justify-center flex-shrink-0"
                                 style="background: #10B98118; border: 1.5px solid #10B981;">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="#10B981" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-sm text-void">{{ $item }}</span>
                        </label>
                    @endforeach
                </div>
                @if($os['etapa_finalizacao']['observacoes'])
                    <div class="p-3 rounded-lg" style="background: var(--color-surface); border: 1px solid var(--color-border);">
                        <p class="text-xs text-muted mb-1">Observações finais</p>
                        <p class="text-sm text-void">{{ $os['etapa_finalizacao']['observacoes'] }}</p>
                    </div>
                @endif
            @else
                <p class="text-muted text-sm">Finalização ainda não registrada.</p>
            @endif
        </div>
        @endif

        {{-- Serviços da OS (sempre visível) --}}
        @if(!empty($os['servicos']))
        <div class="bg-white rounded-xl p-5" style="border: 1px solid var(--color-border);">
            <h3 class="font-display font-semibold text-void text-sm mb-4">Serviços</h3>
            <div class="space-y-2">
                @foreach($os['servicos'] as $servico)
                    @php
                        $statusCor = match($servico['status']) {
                            'concluido'    => ['bg' => '#10B98115', 'text' => '#059669', 'label' => 'Concluído'],
                            'em_andamento' => ['bg' => '#7C3AED15', 'text' => '#6D28D9', 'label' => 'Em andamento'],
                            default        => ['bg' => '#94A3B815', 'text' => '#64748B', 'label' => 'Pendente'],
                        };
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-lg"
                         style="background: var(--color-surface); border: 1px solid var(--color-border);">
                        <div class="flex items-center gap-3">
                            <span class="text-xs px-2 py-0.5 rounded-md font-medium"
                                  style="background: {{ $statusCor['bg'] }}; color: {{ $statusCor['text'] }};">
                                {{ $statusCor['label'] }}
                            </span>
                            <span class="text-sm text-void">{{ $servico['descricao'] }}</span>
                        </div>
                        <span class="font-mono text-sm text-void">
                            R$ {{ number_format($servico['valor'], 2, ',', '.') }}
                        </span>
                    </div>
                @endforeach
                <div class="flex justify-end pt-1">
                    <span class="font-mono font-bold text-void text-sm">
                        Total: R$ {{ number_format($os['total'], 2, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        @endif

    </div>{{-- /coluna principal --}}

    {{-- ===== SIDEBAR DIREITA ===== --}}
    <div class="space-y-4">

        {{-- Dados do cliente --}}
        <div class="bg-white rounded-xl p-4" style="border: 1px solid var(--color-border);">
            <h3 class="font-display font-semibold text-void text-sm mb-3">Cliente</h3>
            <p class="text-void font-medium text-sm">{{ $os['cliente'] }}</p>
            <p class="text-muted text-xs mt-0.5">Queixa: {{ $os['descricao_cliente'] }}</p>
        </div>

        {{-- Histórico de transições --}}
        <div class="bg-white rounded-xl p-4" style="border: 1px solid var(--color-border);">
            <h3 class="font-display font-semibold text-void text-sm mb-3">Histórico de Etapas</h3>

            @if(empty($os['historico_transicoes']))
                <p class="text-muted text-xs">Nenhuma transição registrada.</p>
            @else
                <div class="space-y-0">
                    @foreach(array_reverse($os['historico_transicoes']) as $t)
                        <div class="flex gap-3 relative">
                            {{-- Linha vertical --}}
                            @if(!$loop->last)
                                <div class="absolute left-[7px] top-5 bottom-0 w-px"
                                     style="background: var(--color-border);"></div>
                            @endif
                            <div class="w-3.5 h-3.5 rounded-full mt-1 flex-shrink-0 z-10"
                                 style="background: {{ $etapas[$t['para']]['cor'] }}22; border: 1.5px solid {{ $etapas[$t['para']]['cor'] }};"></div>
                            <div class="pb-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-xs font-medium" style="color: {{ $etapas[$t['de']]['cor'] }};">
                                        {{ $etapas[$t['de']]['label'] }}
                                    </span>
                                    <svg class="w-3 h-3 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <span class="text-xs font-medium" style="color: {{ $etapas[$t['para']]['cor'] }};">
                                        {{ $etapas[$t['para']]['label'] }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-muted mt-0.5">
                                    {{ \Carbon\Carbon::parse($t['em'])->format('d/m H:i') }} · {{ $t['responsavel'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>{{-- /sidebar --}}

</div>{{-- /grid --}}

{{-- ===== MODAL MOVER PARA ===== --}}
<div x-show="moverModal"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(15,23,42,0.6);">
    <div @click.outside="moverModal = false"
         class="bg-white rounded-2xl p-5 w-full max-w-sm shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display font-semibold text-void">Mover para etapa</h3>
            <button @click="moverModal = false" class="text-muted hover:text-void">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="space-y-1.5">
            @foreach($etapas as $key => $etapa)
                <button onclick="alert('Fase 1 — mockado')"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-left transition-colors hover:bg-surface
                               {{ $key === $etapaAtual ? 'cursor-default' : '' }}"
                        style="{{ $key === $etapaAtual ? 'background: var(--color-surface); border: 1px solid var(--color-border);' : '' }}"
                        {{ $key === $etapaAtual ? 'disabled' : '' }}>
                    <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $etapa['cor'] }};"></div>
                    <span class="text-sm {{ $key === $etapaAtual ? 'text-void font-semibold' : 'text-muted' }}">
                        {{ $etapa['label'] }}
                        @if($key === $etapaAtual) <span class="text-xs font-normal">(atual)</span> @endif
                    </span>
                </button>
            @endforeach
        </div>
    </div>
</div>

</div>{{-- /x-data --}}
</x-layouts.oficina>
