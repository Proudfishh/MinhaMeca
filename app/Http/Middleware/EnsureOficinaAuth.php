<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureOficinaAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! session()->has('oficina_auth')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
