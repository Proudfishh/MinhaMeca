<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureClienteAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! session()->has('cliente_auth')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
