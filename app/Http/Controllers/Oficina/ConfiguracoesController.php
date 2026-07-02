<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Mock\MockConfiguracaoService;
use App\Support\Acl;
use Illuminate\Http\Request;

class ConfiguracoesController extends Controller
{
    public function __construct(private MockConfiguracaoService $configService) {}

    public function index(Request $request)
    {
        $tenantId = session('tenant_id', 1);
        $config   = $this->configService->get($tenantId);
        $tabAtiva = $request->query('tab', 'conta');

        $membros = User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get()
            ->load('roles')
            ->map(function (User $u) {
                $papel  = $u->roles->first()?->name;
                $extras = $u->getDirectPermissions()
                    ->pluck('name')
                    ->filter(fn ($p) => str_ends_with($p, '.ver'))
                    ->map(fn ($p) => substr($p, 0, -4))
                    ->values()
                    ->all();

                return [
                    'id'     => $u->id,
                    'nome'   => $u->name,
                    'email'  => $u->email,
                    'papel'  => $papel,
                    'ativo'  => $u->ativo,
                    'extras' => $extras,
                ];
            });

        $papeis  = array_keys(config('acl.roles'));
        $modulos = config('acl.modulos');

        $papelModulos = collect($papeis)
            ->mapWithKeys(fn ($p) => [$p => Acl::modulosComVerDoPreset($p)]);

        return view('oficina.configuracoes.index', compact('config', 'tabAtiva', 'membros', 'papeis', 'modulos', 'papelModulos'));
    }
}
