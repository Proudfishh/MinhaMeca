<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureOficinaAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || ! $user->tenant_id || ! $user->ativo) {
            Auth::logout();

            return redirect()->route('login');
        }

        return $next($request);
    }
}
