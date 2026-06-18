<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MinhaMeca — Selecionar Oficina</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-void relative overflow-hidden">

    <div class="absolute inset-0 pointer-events-none" style="background-image: radial-gradient(circle, rgba(30,58,95,0.4) 1px, transparent 1px); background-size: 24px 24px;"></div>
    <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse at center, transparent 50%, #0F172A 100%);"></div>

    <div class="relative z-10 w-full max-w-sm px-6 py-8">

        <div class="mb-8">
            <p class="font-mono text-[10px] tracking-[0.35em] text-spark uppercase mb-2 select-none">
                Portal do Cliente
            </p>
            <h1 class="font-display text-2xl font-bold text-white leading-tight">
                Olá, {{ $cliente['nome'] }}
            </h1>
            <p class="text-muted text-sm mt-2">
                Você tem veículos em mais de uma oficina.<br>
                Selecione qual deseja acessar:
            </p>
        </div>

        <div class="space-y-3">
            @foreach($cliente['oficinas'] as $oficina)
                <form action="{{ route('login.cliente.selecionar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tenant_id" value="{{ $oficina['id'] }}">
                    <button type="submit"
                            class="w-full text-left px-5 py-4 rounded-xl transition-all group"
                            style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);"
                            onmouseover="this.style.borderColor='rgba(59,130,246,0.4)'; this.style.background='rgba(59,130,246,0.06)'"
                            onmouseout="this.style.borderColor='rgba(255,255,255,0.08)'; this.style.background='rgba(255,255,255,0.04)'">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                 style="background: rgba(30,58,95,0.8);">
                                <svg class="w-4.5 h-4.5 text-spark" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white text-sm font-semibold truncate">{{ $oficina['nome'] }}</p>
                            </div>
                            <svg class="w-4 h-4 text-muted flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </button>
                </form>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="text-xs text-muted hover:text-white/60 transition-colors">
                ← Voltar ao login
            </a>
        </div>

        <p class="text-center mt-8 text-xs select-none" style="color: rgba(100,116,139,0.4);">
            MinhaMeca &copy; {{ date('Y') }}
        </p>
    </div>

</body>
</html>
