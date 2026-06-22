<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'MinhaMeca' }} — Oficina</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-surface min-h-screen flex" x-data="{ sidebarOpen: false, maisOpen: false }">

    {{-- ====================== SIDEBAR ====================== --}}
    <aside
        :class="sidebarOpen ? 'w-60' : 'w-16'"
        class="hidden md:flex flex-shrink-0 flex-col transition-all duration-200 relative z-20"
        style="background: #0F172A; border-right: 1px solid rgba(255,255,255,0.06);">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 h-16 flex-shrink-0" style="border-bottom: 1px solid rgba(255,255,255,0.06);">
            <div class="w-8 h-8 bg-spark rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span x-show="sidebarOpen" class="font-display font-bold text-white text-lg leading-none">MinhaMeca</span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-2 py-4 space-y-0.5 overflow-y-auto">
            @php
                $nav = [
                    ['route' => 'oficina.dashboard',            'label' => 'Dashboard',          'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'oficina.os.index',             'label' => 'Ordens de Serviço',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                    ['route' => 'oficina.clientes.index',       'label' => 'Clientes',           'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['route' => 'oficina.veiculos.index',       'label' => 'Veículos',           'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z'],
                    ['route' => 'oficina.estoque.index',        'label' => 'Estoque',            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['route' => 'oficina.garantias.index',      'label' => 'Garantias',          'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['route' => 'oficina.financeiro.index',     'label' => 'Pendências',         'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['route' => 'oficina.configuracoes.index',  'label' => 'Configurações',      'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ];
            @endphp

            @foreach($nav as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all duration-150 group relative"
                   :class="'{{ $active }}' === '1'
                       ? 'bg-ocean text-white'
                       : 'text-white/40 hover:text-white hover:bg-white/5'"
                   style="{{ $active ? 'background: #1E3A5F; color: #fff;' : '' }}">
                    <svg class="w-4.5 h-4.5 flex-shrink-0 {{ $active ? 'text-white' : 'text-white/40 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span x-show="sidebarOpen" class="truncate font-medium">{{ $item['label'] }}</span>
                    {{-- Tooltip quando collapsed --}}
                    <span x-show="!sidebarOpen"
                          class="absolute left-full ml-2 px-2 py-1 bg-void text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity z-50"
                          style="border: 1px solid rgba(255,255,255,0.1);">
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        </nav>

        {{-- Oficina info + logout --}}
        <div class="px-2 pb-4 flex-shrink-0" style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 12px;">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-7 h-7 bg-ocean rounded-md flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">{{ substr(session('oficina_nome', 'O'), 0, 1) }}</span>
                </div>
                <div x-show="sidebarOpen" class="flex-1 min-w-0">
                    <p class="text-white text-xs font-medium truncate">{{ session('oficina_nome', 'Oficina') }}</p>
                    <p class="text-white/30 text-[10px] truncate">Tenant #{{ session('tenant_id', 1) }}</p>
                </div>
                <form x-show="sidebarOpen" action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
                    @csrf
                    <button type="submit" title="Sair"
                            class="text-white/30 hover:text-white/70 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ====================== MAIN ====================== --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Topbar --}}
        <header class="h-16 flex items-center px-6 flex-shrink-0 bg-white"
                style="border-bottom: 1px solid var(--color-border);">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="text-muted hover:text-void transition-colors mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <h1 class="font-display font-semibold text-void text-base flex-1">
                {{ $title ?? 'Dashboard' }}
            </h1>

            <div class="flex items-center gap-3">
                {{-- Notificações --}}
                <button class="relative text-muted hover:text-void transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-spark rounded-full"></span>
                </button>

                {{-- Avatar --}}
                <div class="w-8 h-8 bg-ocean rounded-full flex items-center justify-center">
                    <span class="text-white text-xs font-bold">{{ substr(session('oficina_nome', 'O'), 0, 1) }}</span>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-6 pb-24 md:pb-6">
            {{ $slot }}
        </main>
    </div>

    {{-- ===== MOBILE BOTTOM NAV (md:hidden) ===== --}}
    {{-- Para transição futura para Drawer (opção B): remover este bloco e adicionar
         <button> hambúrguer na topbar + x-show na sidebar com overlay. --}}
    @php
        $mobileNav = [
            ['route' => 'oficina.dashboard',      'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'oficina.os.index',       'label' => 'OS',        'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
            ['route' => 'oficina.clientes.index', 'label' => 'Clientes',  'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ];
        $maisNav = [
            ['route' => 'oficina.veiculos.index',      'label' => 'Veículos',    'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10a2 2 0 002-2zm0 0V9h4l3 3v4h-7z'],
            ['route' => 'oficina.estoque.index',       'label' => 'Estoque',     'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ['route' => 'oficina.garantias.index',     'label' => 'Garantias',   'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['route' => 'oficina.financeiro.index',    'label' => 'Pendências',  'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['route' => 'oficina.configuracoes.index', 'label' => 'Config.',     'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
        ];
    @endphp

    {{-- Bottom nav bar --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 flex items-center justify-around px-2 py-2 safe-area-bottom"
         style="background: #0F172A; border-top: 1px solid rgba(255,255,255,0.06);">

        @foreach($mobileNav as $item)
            @php $active = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex flex-col items-center gap-1 px-4 py-1.5 rounded-xl transition-all"
               style="{{ $active ? 'background: #1E3A5F;' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="{{ $active ? '#3B82F6' : 'rgba(255,255,255,0.35)' }}" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                </svg>
                <span class="text-[10px] font-medium" style="color: {{ $active ? '#3B82F6' : 'rgba(255,255,255,0.35)' }};">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach

        {{-- Mais --}}
        <button @click="maisOpen = true"
                class="flex flex-col items-center gap-1 px-4 py-1.5 rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="rgba(255,255,255,0.35)" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8m-8 6h16"/>
            </svg>
            <span class="text-[10px] font-medium" style="color: rgba(255,255,255,0.35);">Mais</span>
        </button>
    </nav>

    {{-- Backdrop --}}
    <div x-show="maisOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="maisOpen = false"
         class="md:hidden fixed inset-0 z-40"
         style="background: rgba(0,0,0,0.5);">
    </div>

    {{-- "Mais" bottom sheet --}}
    <div x-show="maisOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="md:hidden fixed bottom-0 left-0 right-0 z-50 rounded-t-2xl pb-8"
         style="background: #0F172A; border-top: 1px solid rgba(255,255,255,0.08);">

        {{-- Handle --}}
        <div class="flex justify-center pt-3 pb-2">
            <div class="w-10 h-1 rounded-full" style="background: rgba(255,255,255,0.2);"></div>
        </div>

        <div class="px-4 pb-2">
            <p class="text-xs font-semibold mb-3" style="color: rgba(255,255,255,0.3); letter-spacing: .5px; text-transform: uppercase;">Mais módulos</p>
            <div class="flex flex-col gap-1">
                @foreach($maisNav as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                       @click="maisOpen = false"
                       class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all"
                       style="{{ $active ? 'background: #1E3A5F;' : 'background: rgba(255,255,255,0.03);' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="{{ $active ? '#3B82F6' : 'rgba(255,255,255,0.5)' }}" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span class="text-sm font-medium" style="color: {{ $active ? '#fff' : 'rgba(255,255,255,0.6)' }};">
                            {{ $item['label'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    {{-- ===== /MOBILE BOTTOM NAV ===== --}}

    @livewireScripts
</body>
</html>
