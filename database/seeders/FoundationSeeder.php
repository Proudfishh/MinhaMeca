<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Support\Acl;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class FoundationSeeder extends Seeder
{
    public function run(): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->forgetCachedPermissions();

        $modulos = config('acl.modulos');

        // 1) Permissões são globais (não pertencem a um tenant no modo teams do Spatie).
        foreach ($modulos as $modulo => $def) {
            foreach ($def['acoes'] as $acao) {
                Permission::firstOrCreate([
                    'name'       => "{$modulo}.{$acao}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // 2) Tenants demo (id 1 casa com os mocks atuais).
        $tenants = [
            ['nome' => 'Auto Center Premium', 'cidade' => 'São Paulo'],
            ['nome' => 'Oficina do Zé',       'cidade' => 'Campinas'],
            ['nome' => 'MecaRápida Express',  'cidade' => 'Santos'],
        ];

        foreach ($tenants as $t) {
            $tenant = Tenant::firstOrCreate(
                ['slug' => Str::slug($t['nome'])],
                ['nome' => $t['nome'], 'cidade' => $t['cidade']],
            );

            // 3) Roles são por tenant (team). Cria os presets de config/acl.php.
            $registrar->setPermissionsTeamId($tenant->id);

            foreach (config('acl.roles') as $roleName => $preset) {
                $role = Role::firstOrCreate([
                    'name'       => $roleName,
                    'guard_name' => 'web',
                    'tenant_id'  => $tenant->id,
                ]);

                $role->syncPermissions(Acl::permissoesDoPreset($preset));
            }
        }

        // 4) Usuários demo no tenant 1, um por cargo, pra validar os acessos.
        $tenant1 = Tenant::where('slug', Str::slug('Auto Center Premium'))->first();
        $registrar->setPermissionsTeamId($tenant1->id);

        $demos = [
            ['name' => 'Gabriel Gerente',  'email' => 'gerente@demo.test',  'cargo' => 'Gerente',  'role' => 'Gerente'],
            ['name' => 'Rita Recepção',    'email' => 'recepcao@demo.test', 'cargo' => 'Recepção', 'role' => 'Recepção'],
            ['name' => 'Marcos Mecânico',  'email' => 'mecanico@demo.test', 'cargo' => 'Mecânico', 'role' => 'Mecânico'],
        ];

        foreach ($demos as $d) {
            $user = User::firstOrCreate(
                ['email' => $d['email']],
                [
                    'tenant_id' => $tenant1->id,
                    'name'      => $d['name'],
                    'cargo'     => $d['cargo'],
                    'ativo'     => true,
                    'password'  => Hash::make('senha123'),
                ],
            );

            $user->syncRoles([$d['role']]);
        }

        $registrar->forgetCachedPermissions();
    }
}
