<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MinhaMeca — Acesso</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen flex items-center justify-center bg-void relative overflow-hidden">

    {{-- Grade de engenharia: pontos radiais sutis, referência a plantas técnicas --}}
    <div class="absolute inset-0 pointer-events-none" style="background-image: radial-gradient(circle, rgba(30,58,95,0.4) 1px, transparent 1px); background-size: 24px 24px;"></div>

    {{-- Vinheta suave nas bordas --}}
    <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse at center, transparent 50%, #0F172A 100%);"></div>

    {{-- Formulário principal --}}
    <div class="relative z-10 w-full max-w-sm px-6 py-8"
         x-data="{ portal: 'oficina', tab: 'login' }">

        {{-- Logo / Masthead --}}
        <div class="mb-10">
            <p class="font-mono text-[10px] tracking-[0.35em] text-spark uppercase mb-2 select-none">
                Sistema de Gestão
            </p>
            <h1 class="font-display text-[2.75rem] font-bold text-white leading-none tracking-tight">
                MinhaMeca
            </h1>
            <p class="text-muted text-sm mt-2">
                Plataforma para oficinas mecânicas
            </p>
        </div>

        {{-- Toggle portal --}}
        <div class="flex rounded-xl p-1 mb-8"
             style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07);">
            <button
                @click="portal = 'oficina'; tab = 'login'"
                :class="portal === 'oficina'
                    ? 'bg-ocean text-white shadow-sm'
                    : 'text-muted hover:text-white/70'"
                class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 flex items-center justify-center gap-2"
            >
                <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Oficina
            </button>
            <button
                @click="portal = 'cliente'"
                :class="portal === 'cliente'
                    ? 'bg-ocean text-white shadow-sm'
                    : 'text-muted hover:text-white/70'"
                class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 flex items-center justify-center gap-2"
            >
                <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
                Cliente
            </button>
        </div>

        {{-- ============================================================ --}}
        {{-- PORTAL: OFICINA --}}
        {{-- ============================================================ --}}
        <div x-show="portal === 'oficina'"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">

            {{-- Sub-tabs --}}
            <div class="flex gap-5 mb-6" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                <button
                    @click="tab = 'login'"
                    :class="tab === 'login' ? 'text-white border-b-2 border-spark' : 'text-muted hover:text-white/70'"
                    class="text-sm font-medium pb-3 -mb-px transition-colors"
                >Entrar</button>
                <button
                    @click="tab = 'criar'"
                    :class="tab === 'criar' ? 'text-white border-b-2 border-spark' : 'text-muted hover:text-white/70'"
                    class="text-sm font-medium pb-3 -mb-px transition-colors"
                >Criar conta</button>
                <button
                    @click="tab = 'senha'"
                    :class="tab === 'senha' ? 'text-white border-b-2 border-spark' : 'text-muted hover:text-white/70'"
                    class="text-sm font-medium pb-3 -mb-px transition-colors"
                >Esqueci a senha</button>
            </div>

            {{-- Tab: Login --}}
            <form x-show="tab === 'login'"
                  action="{{ route('login.oficina') }}" method="POST"
                  class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">Email</label>
                    <input
                        type="email" name="email"
                        placeholder="contato@suaoficina.com.br"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">Senha</label>
                    <input
                        type="password" name="password"
                        placeholder="••••••••"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <button type="submit"
                        class="w-full bg-spark hover:bg-blue-500 active:bg-blue-700 text-white font-medium rounded-lg py-3 text-sm transition-colors mt-1">
                    Entrar na oficina
                </button>
            </form>

            {{-- Tab: Criar conta --}}
            <form x-show="tab === 'criar'"
                  action="#" method="POST"
                  class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">Nome da oficina</label>
                    <input
                        type="text" name="nome"
                        placeholder="Auto Center Premium"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">Email</label>
                    <input
                        type="email" name="email"
                        placeholder="contato@suaoficina.com.br"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">Senha</label>
                    <input
                        type="password" name="password"
                        placeholder="••••••••"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <button type="submit"
                        class="w-full bg-spark hover:bg-blue-500 active:bg-blue-700 text-white font-medium rounded-lg py-3 text-sm transition-colors mt-1">
                    Criar minha conta
                </button>
            </form>

            {{-- Tab: Esqueci a senha --}}
            <form x-show="tab === 'senha'"
                  action="#" method="POST"
                  class="space-y-4">
                @csrf
                <p class="text-muted text-sm leading-relaxed">
                    Informe seu email e enviaremos as instruções para redefinir a senha.
                </p>
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">Email</label>
                    <input
                        type="email" name="email"
                        placeholder="contato@suaoficina.com.br"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <button type="submit"
                        class="w-full bg-spark hover:bg-blue-500 active:bg-blue-700 text-white font-medium rounded-lg py-3 text-sm transition-colors mt-1">
                    Enviar instruções
                </button>
            </form>

        </div>{{-- /portal oficina --}}

        {{-- ============================================================ --}}
        {{-- PORTAL: CLIENTE --}}
        {{-- ============================================================ --}}
        <div x-show="portal === 'cliente'"
             x-data="{ tabC: 'entrar' }"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">

            {{-- Sub-tabs --}}
            <div class="flex gap-5 mb-6" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                <button
                    @click="tabC = 'entrar'"
                    :class="tabC === 'entrar' ? 'text-white border-b-2 border-spark' : 'text-muted hover:text-white/70'"
                    class="text-sm font-medium pb-3 -mb-px transition-colors"
                >Entrar</button>
                <button
                    @click="tabC = 'criar'"
                    :class="tabC === 'criar' ? 'text-white border-b-2 border-spark' : 'text-muted hover:text-white/70'"
                    class="text-sm font-medium pb-3 -mb-px transition-colors"
                >Criar conta</button>
                <button
                    @click="tabC = 'senha'"
                    :class="tabC === 'senha' ? 'text-white border-b-2 border-spark' : 'text-muted hover:text-white/70'"
                    class="text-sm font-medium pb-3 -mb-px transition-colors"
                >Esqueci a senha</button>
            </div>

            {{-- Tab: Entrar --}}
            <form x-show="tabC === 'entrar'"
                  action="{{ route('login.cliente') }}" method="POST"
                  class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">CPF</label>
                    <input
                        type="text" name="cpf"
                        value="{{ old('cpf') }}"
                        placeholder="000.000.000-00"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm font-mono placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">Email</label>
                    <input
                        type="email" name="email"
                        value="{{ old('email') }}"
                        placeholder="seuemail@email.com"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>

                @error('cpf')
                    <p class="text-red-400 text-xs">{{ $message }}</p>
                @enderror

                <button type="submit"
                        class="w-full bg-spark hover:bg-blue-500 active:bg-blue-700 text-white font-medium rounded-lg py-3 text-sm transition-colors mt-1">
                    Ver status do meu carro
                </button>
            </form>

            {{-- Tab: Criar conta --}}
            <form x-show="tabC === 'criar'"
                  action="#" method="POST"
                  class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">Nome completo</label>
                    <input
                        type="text" name="nome"
                        placeholder="Seu nome"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">CPF</label>
                    <input
                        type="text" name="cpf"
                        placeholder="000.000.000-00"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm font-mono placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">Email</label>
                    <input
                        type="email" name="email"
                        placeholder="seuemail@email.com"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">Senha</label>
                    <input
                        type="password" name="password"
                        placeholder="••••••••"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <button type="submit"
                        class="w-full bg-spark hover:bg-blue-500 active:bg-blue-700 text-white font-medium rounded-lg py-3 text-sm transition-colors mt-1">
                    Criar minha conta
                </button>
            </form>

            {{-- Tab: Esqueci a senha --}}
            <form x-show="tabC === 'senha'"
                  action="#" method="POST"
                  class="space-y-4">
                @csrf
                <p class="text-muted text-sm leading-relaxed">
                    Informe seu CPF e e-mail para receber as instruções de recuperação.
                </p>
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">CPF</label>
                    <input
                        type="text" name="cpf"
                        placeholder="000.000.000-00"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm font-mono placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <div>
                    <label class="block text-xs text-muted mb-1.5 tracking-wide">Email</label>
                    <input
                        type="email" name="email"
                        placeholder="seuemail@email.com"
                        class="w-full rounded-lg px-4 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none transition-all"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09);"
                        onfocus="this.style.borderColor='#3B82F6'; this.style.background='rgba(59,130,246,0.06)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.05)'"
                    >
                </div>
                <button type="submit"
                        class="w-full bg-spark hover:bg-blue-500 active:bg-blue-700 text-white font-medium rounded-lg py-3 text-sm transition-colors mt-1">
                    Enviar instruções
                </button>
            </form>

        </div>{{-- /portal cliente --}}

        {{-- Rodapé --}}
        <p class="text-center mt-10 text-xs select-none"
           style="color: rgba(100,116,139,0.4);">
            MinhaMeca &copy; {{ date('Y') }}
        </p>

    </div>

    @livewireScripts
</body>
</html>
