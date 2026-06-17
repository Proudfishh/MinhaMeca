<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $tenantId = session('tenant_id', 1);
        $request->attributes->set('tenant_id', $tenantId);

        return $next($request);
    }
}
