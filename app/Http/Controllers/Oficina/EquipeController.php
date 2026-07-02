<?php

namespace App\Http\Controllers\Oficina;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Acl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EquipeController extends Controller
{
    public function store(Request $request)
    {
        $tenantId = session('tenant_id', 1);
        $papeis   = array_keys(config('acl.roles'));

        $data = $request->validate([
            'nome'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'senha' => ['required', 'string', 'min:6'],
            'papel' => ['required', Rule::in($papeis)],
        ]);

        $user = User::create([
            'tenant_id' => $tenantId,
            'name'      => $data['nome'],
            'email'     => $data['email'],
            'cargo'     => $data['papel'],
            'ativo'     => true,
            'password'  => Hash::make($data['senha']),
        ]);

        $user->syncRoles([$data['papel']]);

        return back()->with('sucesso', "{$user->name} cadastrado(a) como {$data['papel']}.");
    }

    public function update(Request $request, User $user)
    {
        $this->autorizarMembroDoTenant($user);

        $papeis = array_keys(config('acl.roles'));

        $data = $request->validate([
            'papel'   => ['required', Rule::in($papeis)],
            'extras'  => ['array'],
            'extras.*' => ['string'],
        ]);

        // Só aceita como "extra" um módulo que o preset do papel ainda não cobre —
        // evita que o form manipulado conceda algo fora do catálogo.
        $disponiveis = array_diff(
            array_keys(config('acl.modulos')),
            Acl::modulosComVerDoPreset($data['papel']),
        );
        $extras = array_values(array_intersect($data['extras'] ?? [], $disponiveis));

        $user->cargo = $data['papel'];
        $user->save();

        $user->syncRoles([$data['papel']]);
        $user->syncPermissions(array_map(fn ($m) => "{$m}.ver", $extras));

        return back()->with('sucesso', "Acesso de {$user->name} atualizado.");
    }

    public function toggleAtivo(User $user)
    {
        $this->autorizarMembroDoTenant($user);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['equipe' => 'Você não pode desativar sua própria conta.']);
        }

        $user->ativo = ! $user->ativo;
        $user->save();

        $status = $user->ativo ? 'ativado(a)' : 'desativado(a)';

        return back()->with('sucesso', "{$user->name} foi {$status}.");
    }

    private function autorizarMembroDoTenant(User $user): void
    {
        abort_unless($user->tenant_id === session('tenant_id', 1), 403);
    }
}
