<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Funcionário da oficina: o tenant vem do usuário autenticado, não da sessão.
            $tenantId = Auth::user()->tenant_id;
            session(['tenant_id' => $tenantId]);
            app(PermissionRegistrar::class)->setPermissionsTeamId($tenantId);
        } else {
            // Portal do cliente: segue no fluxo de sessão mockado por enquanto.
            $tenantId = session('tenant_id', 1);
        }

        $request->attributes->set('tenant_id', $tenantId);

        return $next($request);
    }
}
