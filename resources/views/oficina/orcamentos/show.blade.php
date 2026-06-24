<x-layouts.oficina :title="$orc['codigo']">

@php
    $statusMap = [
        'aprovado' => ['bg' => 'rgba(16,185,129,0.1)',  'text' => '#059669', 'label' => 'Aprovado'],
        'pendente' => ['bg' => 'rgba(245,158,11,0.1)',  'text' => '#D97706', 'label' => 'Pendente'],
        'rascunho' => ['bg' => 'rgba(100,116,139,0.1)', 'text' => '#64748b', 'label' => 'Rascunho'],
    ];
    $status = $statusMap[$orc['status']] ?? $statusMap['rascunho'];

    $tipoConfig = [
        'peca'    => ['label' => 'Peças',    'color' => '#3b82f6', 'bg' => 'rgba(59,130,246,0.08)'],
        'servico' => ['label' => 'Serviços', 'color' => '#7c3aed', 'bg' => 'rgba(124,58,237,0.08)'],
        'outro'   => ['label' => 'Outros',   'color' => '#64748b', 'bg' => 'rgba(100,116,139,0.08)'],
    ];

    $grupos = collect($orc['itens'])->groupBy(fn($i) => $i['tipo'] ?? 'outro');
@endphp

<div x-data="{
    sheetStatusAberto:  false,
    sheetOsAberto:      false,
    sheetExcluirAberto: false,
    sheetEditarAberto:  false,
    sheetPdfAberto:     false,
    pdfCarregando:      false,
    pdfPronto:          false,
    osSelecionada:      '{{ $orc['os_vinculada'] ?? '' }}',
    novoStatus:         '{{ $orc['status'] }}',
    buscaOs:            '',
    osAbertas:          {{ Js::from($osAbertas) }},
    statusLabels:       { aprovado: 'Aprovado', pendente: 'Pendente', rascunho: 'Rascunho' },

    get osFiltradas() {
        const q = this.buscaOs.toLowerCase().normalize('NFD').replace(/\p{M}/gu, '');
        if (!q) return this.osAbertas;
        return this.osAbertas.filter(os =>
            os.id.toLowerCase().includes(q) ||
            os.cliente.toLowerCase().normalize('NFD').replace(/\p{M}/gu, '').includes(q) ||
            os.veiculo.toLowerCase().normalize('NFD').replace(/\p{M}/gu, '').includes(q)
        );
    },

    gerarPdf() {
        this.pdfCarregando = true;
        setTimeout(() => { this.pdfCarregando = false; this.pdfPronto = true; }, 1800);
    },
}">

{{-- ============================================================
     HEADER
============================================================ --}}
<div class="flex items-center gap-2 mb-5">
    <a href="{{ route('oficina.orcamentos.index') }}"
       class="flex items-center gap-1.5 text-sm text-muted hover:text-void transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Orçamentos
    </a>
    <span class="text-muted/40">·</span>
    <span class="font-mono font-bold text-void text-sm">{{ $orc['codigo'] }}</span>
    <span class="text-xs px-2 py-0.5 rounded-full font-semibold ml-1"
          style="background:{{ $status['bg'] }};color:{{ $status['text'] }};">
        {{ $status['label'] }}
    </span>
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

<div class="max-w-2xl mx-auto pb-28 md:pb-8">

    {{-- ── Dados do orçamento ── --}}
    <div class="bg-white rounded-2xl mb-3" style="border:1px solid var(--color-border);box-shadow:0 1px 6px rgba(0,0,0,0.04);">
        <div class="px-4 pt-4 pb-4">
            <p class="text-[10px] font-semibold text-muted uppercase tracking-wide mb-3">Informações</p>
            <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                @if($orc['cliente'])
                <div>
                    <p class="text-[10px] text-muted font-medium uppercase tracking-wide">Cliente</p>
                    <p class="font-semibold text-void text-sm mt-0.5">{{ $orc['cliente'] }}</p>
                </div>
                @endif

                @if($orc['veiculo'])
                <div>
                    <p class="text-[10px] text-muted font-medium uppercase tracking-wide">Veículo</p>
                    <p class="font-semibold text-void text-sm mt-0.5">{{ $orc['veiculo'] }}</p>
                    @if($orc['placa'])
                    <p class="font-mono text-xs text-muted">{{ $orc['placa'] }}</p>
                    @endif
                </div>
                @endif

                <div>
                    <p class="text-[10px] text-muted font-medium uppercase tracking-wide">Emitido em</p>
                    <p class="font-semibold text-void text-sm mt-0.5">
                        {{ \Carbon\Carbon::parse($orc['criado_em'])->format('d/m/Y') }}
                    </p>
                </div>

                <div>
                    <p class="text-[10px] text-muted font-medium uppercase tracking-wide">Válido até</p>
                    <p class="font-semibold text-void text-sm mt-0.5">
                        {{ \Carbon\Carbon::parse($orc['validade'])->format('d/m/Y') }}
                    </p>
                </div>

                @if($orc['os_vinculada'])
                <div class="col-span-2">
                    <p class="text-[10px] text-muted font-medium uppercase tracking-wide">OS vinculada</p>
                    <p class="font-mono font-semibold text-sm mt-0.5" style="color:#3b82f6;">
                        {{ $orc['os_vinculada'] }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Itens agrupados por categoria (Opção B) ── --}}
    <div class="bg-white rounded-2xl mb-3 overflow-hidden" style="border:1px solid var(--color-border);box-shadow:0 1px 6px rgba(0,0,0,0.04);">

        @foreach($grupos as $tipo => $itens)
        @php
            $cfg      = $tipoConfig[$tipo] ?? $tipoConfig['outro'];
            $subtotal = $itens->sum(fn($i) => $i['preco'] * $i['qtd']);
            $isLast   = $loop->last;
        @endphp

        {{-- Cabeçalho do grupo --}}
        <div class="flex items-center justify-between px-4 py-3"
             style="background:#fafafa;border-bottom:1px solid var(--color-border);{{ !$loop->first ? 'border-top:1px solid var(--color-border);' : '' }}">
            <div class="flex items-center gap-2.5">
                {{-- ícone por tipo --}}
                <div class="w-6 h-6 rounded-md flex items-center justify-center flex-shrink-0"
                     style="background:{{ $cfg['bg'] }};">
                    @if($tipo === 'peca')
                    <svg class="w-3.5 h-3.5" style="color:{{ $cfg['color'] }};" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    @elseif($tipo === 'servico')
                    <svg class="w-3.5 h-3.5" style="color:{{ $cfg['color'] }};" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    @else
                    <svg class="w-3.5 h-3.5" style="color:{{ $cfg['color'] }};" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    @endif
                </div>
                <span class="text-[11px] font-bold uppercase tracking-wide" style="color:{{ $cfg['color'] }};">
                    {{ $cfg['label'] }}
                </span>
                <span class="text-[10px] font-semibold text-muted">{{ $itens->count() }}</span>
            </div>
            <span class="font-mono font-semibold text-sm text-muted">
                R$ {{ number_format($subtotal, 2, ',', '.') }}
            </span>
        </div>

        {{-- Itens do grupo --}}
        @foreach($itens as $j => $item)
        @php $itemSubtotal = $item['preco'] * $item['qtd']; @endphp
        <div class="flex items-center gap-3 px-4 py-3.5"
             style="{{ (!$loop->last || !$isLast) ? 'border-bottom:1px solid rgba(0,0,0,0.04);' : '' }}">
            <div class="flex-1 min-w-0">
                <p class="font-medium text-void text-sm leading-snug">{{ $item['descricao'] }}</p>
                <p class="text-[11px] font-mono mt-0.5" style="color:var(--color-muted,#94a3b8);">
                    {{ $item['qtd'] }}× · R$ {{ number_format($item['preco'], 2, ',', '.') }}/un.
                </p>
            </div>
            <p class="font-mono font-semibold text-void text-sm flex-shrink-0">
                R$ {{ number_format($itemSubtotal, 2, ',', '.') }}
            </p>
        </div>
        @endforeach

        @endforeach

        {{-- Total --}}
        <div class="flex items-center justify-between px-4 py-3.5" style="background:#0f172a;">
            <p class="text-xs font-semibold uppercase tracking-wide" style="color:rgba(255,255,255,0.35);">Total</p>
            <p class="font-mono font-bold text-white text-xl">
                R$ {{ number_format($orc['total'], 2, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- ── Ações ── --}}
    <div class="bg-white rounded-2xl mb-3" style="border:1px solid var(--color-border);box-shadow:0 1px 6px rgba(0,0,0,0.04);">
        <p class="px-4 pt-4 pb-2 text-[10px] font-semibold text-muted uppercase tracking-wide">Ações</p>

        {{-- Alterar status --}}
        <button @click="sheetStatusAberto = true"
                class="w-full flex items-center gap-3 px-4 py-3.5 text-left hover:bg-surface transition-colors"
                style="border-bottom:1px solid var(--color-border);">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(59,130,246,0.08);">
                <svg class="w-4 h-4" style="color:#3b82f6;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-void text-sm">Alterar status</p>
                <p class="text-xs text-muted">Atual: <span style="color:{{ $status['text'] }};">{{ $status['label'] }}</span></p>
            </div>
            <svg class="w-4 h-4 flex-shrink-0 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
            </svg>
        </button>

        {{-- Editar itens --}}
        <button @click="sheetEditarAberto = true"
                class="w-full flex items-center gap-3 px-4 py-3.5 text-left hover:bg-surface transition-colors"
                style="border-bottom:1px solid var(--color-border);">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,0.08);">
                <svg class="w-4 h-4" style="color:#059669;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-void text-sm">Editar orçamento</p>
                <p class="text-xs text-muted">Itens, quantidades e valores</p>
            </div>
            <svg class="w-4 h-4 flex-shrink-0 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
            </svg>
        </button>

        {{-- Vincular OS --}}
        <button @click="sheetOsAberto = true"
                class="w-full flex items-center gap-3 px-4 py-3.5 text-left hover:bg-surface transition-colors"
                style="border-bottom:1px solid var(--color-border);">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(124,58,237,0.08);">
                <svg class="w-4 h-4" style="color:#7c3aed;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 8h6"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-void text-sm">{{ $orc['os_vinculada'] ? 'Alterar OS vinculada' : 'Vincular a uma OS' }}</p>
                <p class="text-xs text-muted">
                    @if($orc['os_vinculada'])
                        <span style="color:#7c3aed;">{{ $orc['os_vinculada'] }}</span>
                    @else
                        Nenhuma OS vinculada
                    @endif
                </p>
            </div>
            <svg class="w-4 h-4 flex-shrink-0 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
            </svg>
        </button>

        {{-- Baixar PDF --}}
        <button @click="sheetPdfAberto = true; pdfCarregando = false; pdfPronto = false;"
                class="w-full flex items-center gap-3 px-4 py-3.5 text-left hover:bg-surface transition-colors"
                style="border-bottom:1px solid var(--color-border);">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,0.08);">
                <svg class="w-4 h-4" style="color:#D97706;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-void text-sm">Baixar PDF</p>
                <p class="text-xs text-muted">Gera orçamento para envio ao cliente</p>
            </div>
            <svg class="w-4 h-4 flex-shrink-0 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
            </svg>
        </button>

        {{-- Excluir --}}
        <button @click="sheetExcluirAberto = true"
                class="w-full flex items-center gap-3 px-4 py-3.5 text-left hover:bg-surface transition-colors rounded-b-2xl">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(239,68,68,0.08);">
                <svg class="w-4 h-4" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm" style="color:#ef4444;">Excluir orçamento</p>
                <p class="text-xs text-muted">Esta ação não pode ser desfeita</p>
            </div>
            <svg class="w-4 h-4 flex-shrink-0 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
            </svg>
        </button>
    </div>

</div>{{-- /max-w-2xl --}}

{{-- ============================================================
     BOTTOM SHEET — ALTERAR STATUS
============================================================ --}}
<div x-show="sheetStatusAberto"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="sheetStatusAberto = false"
     class="fixed inset-0 z-40" style="background:rgba(0,0,0,0.5);" x-cloak></div>
<div x-show="sheetStatusAberto"
     x-transition:enter="transition ease-out duration-250" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
     @click.stop
     class="fixed bottom-0 left-0 right-0 z-50 rounded-t-2xl px-4 pb-8 pt-3" style="background:#fff;" x-cloak>

    <div class="flex justify-center mb-4">
        <div class="w-10 h-1 rounded-full" style="background:rgba(0,0,0,0.1);"></div>
    </div>
    <p class="font-bold text-void text-base mb-4">Alterar status</p>

    <div class="space-y-2 mb-5">
        @foreach([
            'rascunho' => ['label'=>'Rascunho', 'desc'=>'Em elaboração, não enviado',         'color'=>'#64748b', 'border'=>'#64748b'],
            'pendente' => ['label'=>'Pendente', 'desc'=>'Aguardando aprovação do cliente',     'color'=>'#D97706', 'border'=>'#f59e0b'],
            'aprovado' => ['label'=>'Aprovado', 'desc'=>'Cliente aprovou, pronto para execução','color'=>'#059669','border'=>'#10b981'],
        ] as $val => $info)
        <button @click="novoStatus = '{{ $val }}'"
                class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-left transition-all"
                :style="novoStatus === '{{ $val }}'
                    ? 'border:2px solid {{ $info['border'] }};background:rgba(0,0,0,0.02);'
                    : 'border:1px solid var(--color-border);background:var(--color-surface);'">
            <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $info['color'] }};"></div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-void text-sm">{{ $info['label'] }}</p>
                <p class="text-xs text-muted">{{ $info['desc'] }}</p>
            </div>
            <svg x-show="novoStatus === '{{ $val }}'" class="w-4 h-4 flex-shrink-0"
                 style="color:{{ $info['color'] }};" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </button>
        @endforeach
    </div>

    <form method="POST" action="{{ route('oficina.orcamentos.update', $orc['id']) }}">
        @csrf
        <input type="hidden" name="acao" value="status">
        <input type="hidden" :name="'status'" :value="novoStatus">
        <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white" style="background:#0f172a;">
            Salvar alteração
        </button>
    </form>
</div>

{{-- ============================================================
     BOTTOM SHEET — EDITAR ITENS
============================================================ --}}
<div x-show="sheetEditarAberto"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="sheetEditarAberto = false"
     class="fixed inset-0 z-40" style="background:rgba(0,0,0,0.5);" x-cloak></div>
<div x-show="sheetEditarAberto"
     x-transition:enter="transition ease-out duration-250" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
     @click.stop
     class="fixed bottom-0 left-0 right-0 z-50 rounded-t-2xl px-4 pb-8 pt-3" style="background:#fff;max-height:90vh;overflow-y:auto;" x-cloak>

    <div class="flex justify-center mb-4">
        <div class="w-10 h-1 rounded-full" style="background:rgba(0,0,0,0.1);"></div>
    </div>
    <div class="flex items-center justify-between mb-4">
        <p class="font-bold text-void text-base">Editar itens</p>
        <span class="text-xs text-muted">{{ count($orc['itens']) }} itens</span>
    </div>

    <form method="POST" action="{{ route('oficina.orcamentos.update', $orc['id']) }}">
        @csrf
        <input type="hidden" name="acao" value="editar">

        <div class="space-y-2 mb-5">
            @foreach($orc['itens'] as $idx => $item)
            @php $cfg = $tipoConfig[$item['tipo'] ?? 'outro'] ?? $tipoConfig['outro']; @endphp
            <div class="rounded-xl overflow-hidden" style="border:1px solid var(--color-border);">
                {{-- label do tipo --}}
                <div class="px-3 py-1.5 flex items-center gap-1.5" style="background:#fafafa;border-bottom:1px solid var(--color-border);">
                    <div class="w-1.5 h-1.5 rounded-full" style="background:{{ $cfg['color'] }};"></div>
                    <span class="text-[10px] font-bold uppercase tracking-wide" style="color:{{ $cfg['color'] }};">{{ $cfg['label'] }}</span>
                </div>
                {{-- campos --}}
                <div class="p-3 space-y-2">
                    <div>
                        <label class="text-[10px] font-semibold text-muted uppercase tracking-wide block mb-1">Descrição</label>
                        <input type="text" name="itens[{{ $idx }}][descricao]"
                               value="{{ $item['descricao'] }}"
                               class="w-full px-3 py-2 rounded-lg text-sm text-void bg-white focus:outline-none focus:ring-2 focus:ring-blue-300"
                               style="border:1px solid var(--color-border);">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] font-semibold text-muted uppercase tracking-wide block mb-1">Qtd</label>
                            <input type="number" name="itens[{{ $idx }}][qtd]"
                                   value="{{ $item['qtd'] }}" min="1"
                                   class="w-full px-3 py-2 rounded-lg text-sm text-void bg-white focus:outline-none focus:ring-2 focus:ring-blue-300 font-mono"
                                   style="border:1px solid var(--color-border);">
                        </div>
                        <div>
                            <label class="text-[10px] font-semibold text-muted uppercase tracking-wide block mb-1">Preço unit.</label>
                            <input type="number" name="itens[{{ $idx }}][preco]"
                                   value="{{ $item['preco'] }}" step="0.01" min="0"
                                   class="w-full px-3 py-2 rounded-lg text-sm text-void bg-white focus:outline-none focus:ring-2 focus:ring-blue-300 font-mono"
                                   style="border:1px solid var(--color-border);">
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white" style="background:#059669;">
            Salvar alterações
        </button>
    </form>
</div>

{{-- ============================================================
     BOTTOM SHEET — VINCULAR OS  (com busca)
============================================================ --}}
<div x-show="sheetOsAberto"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="sheetOsAberto = false"
     class="fixed inset-0 z-40" style="background:rgba(0,0,0,0.5);" x-cloak></div>
<div x-show="sheetOsAberto"
     x-transition:enter="transition ease-out duration-250" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
     @click.stop
     class="fixed bottom-0 left-0 right-0 z-50 rounded-t-2xl px-4 pb-8 pt-3" style="background:#fff;max-height:85vh;display:flex;flex-direction:column;" x-cloak>

    <div class="flex justify-center mb-3 flex-shrink-0">
        <div class="w-10 h-1 rounded-full" style="background:rgba(0,0,0,0.1);"></div>
    </div>
    <p class="font-bold text-void text-base mb-3 flex-shrink-0">Vincular a OS</p>

    {{-- Barra de busca --}}
    <div class="relative mb-3 flex-shrink-0">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
             style="color:#94a3b8;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
        </svg>
        <input type="text" x-model="buscaOs"
               placeholder="Buscar por OS, cliente ou veículo…"
               class="w-full pl-9 pr-9 py-2.5 rounded-xl text-sm text-void placeholder-muted bg-white focus:outline-none focus:ring-2"
               style="border:1px solid var(--color-border);">
        <button x-show="buscaOs.length > 0" @click="buscaOs = ''"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-void">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Lista rolável --}}
    <div class="overflow-y-auto flex-1 space-y-2 pb-1 mb-4">
        {{-- Nenhuma --}}
        <button @click="osSelecionada = ''"
                class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-left transition-all"
                :style="osSelecionada === '' ? 'border:2px solid #0f172a;background:rgba(15,23,42,0.03);' : 'border:1px solid var(--color-border);background:var(--color-surface);'">
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-void text-sm">Nenhuma</p>
                <p class="text-xs text-muted">Orçamento avulso / sem OS</p>
            </div>
            <svg x-show="osSelecionada === ''" class="w-4 h-4 flex-shrink-0 text-void" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </button>

        <template x-for="os in osFiltradas" :key="os.id">
            <button @click="osSelecionada = os.id"
                    class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-left transition-all"
                    :style="osSelecionada === os.id ? 'border:2px solid #7c3aed;background:rgba(124,58,237,0.04);' : 'border:1px solid var(--color-border);background:var(--color-surface);'">
                <div class="flex-1 min-w-0">
                    <p class="font-mono font-semibold text-sm"
                       :style="osSelecionada === os.id ? 'color:#7c3aed;' : 'color:#0f172a;'"
                       x-text="os.id"></p>
                    <p class="text-xs text-muted mt-0.5" x-text="os.cliente + ' · ' + os.veiculo"></p>
                </div>
                <svg x-show="osSelecionada === os.id" class="w-4 h-4 flex-shrink-0" style="color:#7c3aed;"
                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </template>

        {{-- Sem resultados --}}
        <div x-show="osFiltradas.length === 0 && buscaOs.length > 0"
             class="text-center py-8">
            <p class="text-sm font-semibold text-void mb-1">Nenhuma OS encontrada</p>
            <p class="text-xs text-muted">Tente buscar por número, cliente ou veículo</p>
        </div>
    </div>

    <form method="POST" action="{{ route('oficina.orcamentos.update', $orc['id']) }}" class="flex-shrink-0">
        @csrf
        <input type="hidden" name="acao" value="os">
        <input type="hidden" :name="'os_vinculada'" :value="osSelecionada">
        <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white" style="background:#7c3aed;">
            Salvar vínculo
        </button>
    </form>
</div>

{{-- ============================================================
     BOTTOM SHEET — BAIXAR PDF
============================================================ --}}
<div x-show="sheetPdfAberto"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="sheetPdfAberto = false"
     class="fixed inset-0 z-40" style="background:rgba(0,0,0,0.5);" x-cloak></div>
<div x-show="sheetPdfAberto"
     x-transition:enter="transition ease-out duration-250" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
     @click.stop
     class="fixed bottom-0 left-0 right-0 z-50 rounded-t-2xl px-4 pb-8 pt-3" style="background:#fff;" x-cloak>

    <div class="flex justify-center mb-4">
        <div class="w-10 h-1 rounded-full" style="background:rgba(0,0,0,0.1);"></div>
    </div>

    {{-- Estado: pronto para gerar --}}
    <div x-show="!pdfCarregando && !pdfPronto">
        <p class="font-bold text-void text-base mb-1">Gerar PDF</p>
        <p class="text-sm text-muted mb-5">O orçamento <span class="font-mono font-semibold text-void">{{ $orc['codigo'] }}</span> será exportado com todos os itens e dados do cliente.</p>

        <div class="rounded-xl p-4 mb-5 space-y-2" style="background:#fafafa;border:1px solid var(--color-border);">
            <div class="flex items-center gap-2 text-sm text-void">
                <svg class="w-4 h-4 text-muted flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Dados do cliente e veículo
            </div>
            <div class="flex items-center gap-2 text-sm text-void">
                <svg class="w-4 h-4 text-muted flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Lista de itens com preços unitários
            </div>
            <div class="flex items-center gap-2 text-sm text-void">
                <svg class="w-4 h-4 text-muted flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Total e validade do orçamento
            </div>
        </div>

        <button @click="gerarPdf()"
                class="w-full py-3 rounded-xl text-sm font-bold text-white flex items-center justify-center gap-2"
                style="background:#D97706;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Gerar PDF
        </button>
    </div>

    {{-- Estado: carregando --}}
    <div x-show="pdfCarregando" class="flex flex-col items-center py-8 gap-4">
        <div class="w-12 h-12 rounded-full border-4 border-amber-200 border-t-amber-500 animate-spin"></div>
        <p class="text-sm font-semibold text-void">Gerando PDF…</p>
        <p class="text-xs text-muted">Isso pode levar alguns segundos</p>
    </div>

    {{-- Estado: pronto para download --}}
    <div x-show="pdfPronto" class="flex flex-col items-center text-center py-4 gap-4">
        <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(217,119,6,0.1);">
            <svg class="w-7 h-7" style="color:#D97706;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-void text-base">PDF pronto!</p>
            <p class="text-xs text-muted mt-1 font-mono">{{ $orc['codigo'] }}.pdf · 84 KB</p>
        </div>
        <a href="#"
           onclick="event.preventDefault(); alert('Download iniciado (mock)');"
           class="w-full py-3 rounded-xl text-sm font-bold text-white flex items-center justify-center gap-2"
           style="background:#D97706;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Baixar arquivo
        </a>
        <button @click="sheetPdfAberto = false" class="text-sm text-muted hover:text-void">Fechar</button>
    </div>
</div>

{{-- ============================================================
     BOTTOM SHEET — CONFIRMAR EXCLUSÃO
============================================================ --}}
<div x-show="sheetExcluirAberto"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="sheetExcluirAberto = false"
     class="fixed inset-0 z-40" style="background:rgba(0,0,0,0.5);" x-cloak></div>
<div x-show="sheetExcluirAberto"
     x-transition:enter="transition ease-out duration-250" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
     @click.stop
     class="fixed bottom-0 left-0 right-0 z-50 rounded-t-2xl px-4 pb-8 pt-3" style="background:#fff;" x-cloak>

    <div class="flex justify-center mb-4">
        <div class="w-10 h-1 rounded-full" style="background:rgba(0,0,0,0.1);"></div>
    </div>

    <div class="flex flex-col items-center text-center mb-6">
        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3" style="background:rgba(239,68,68,0.1);">
            <svg class="w-6 h-6" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
        <p class="font-bold text-void text-base">Excluir {{ $orc['codigo'] }}?</p>
        <p class="text-sm text-muted mt-1">Esta ação não pode ser desfeita. O orçamento será removido permanentemente.</p>
    </div>

    <div class="flex gap-2">
        <button @click="sheetExcluirAberto = false"
                class="flex-1 py-3 rounded-xl text-sm font-semibold text-void transition-colors"
                style="border:1px solid var(--color-border);background:var(--color-surface);">
            Cancelar
        </button>
        <form method="POST" action="{{ route('oficina.orcamentos.update', $orc['id']) }}" class="flex-1">
            @csrf
            <input type="hidden" name="acao" value="excluir">
            <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white" style="background:#ef4444;">
                Excluir
            </button>
        </form>
    </div>
</div>

</div>{{-- /x-data --}}

</x-layouts.oficina>
