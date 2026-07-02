<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\Nav;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

class OficinaAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Credenciais inválidas.'])
                ->onlyInput('email');
        }

        $user = Auth::user();

        if (! $user->tenant_id || ! $user->ativo) {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'Usuário sem acesso liberado.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        session([
            'oficina_nome' => $user->tenant->nome,
            'tenant_id'    => $user->tenant_id,
        ]);

        app(PermissionRegistrar::class)->setPermissionsTeamId($user->tenant_id);

        return redirect()->route(Nav::rotaInicial($user));
    }
}
