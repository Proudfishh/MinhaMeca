<?php

namespace App\Support;

use App\Models\User;

class Nav
{
    /**
     * Itens de navegação que o usuário pode acessar, na ordem de config/acl.php.
     * Cada item já carrega tudo que a view precisa (rota, label, ícone, posição mobile).
     *
     * @return array<int, array{modulo:string, route:string, label:string, label_mobile:string, icon:string, mobile:string}>
     */
    public static function itensPermitidos(User $user): array
    {
        $itens = [];

        foreach (config('acl.modulos') as $modulo => $def) {
            if (! $user->can("{$modulo}.ver")) {
                continue;
            }

            $itens[] = [
                'modulo'       => $modulo,
                'route'        => $def['rota'],
                'label'        => $def['label'],
                'label_mobile' => $def['label_mobile'] ?? $def['label'],
                'icon'         => $def['icon'],
                'mobile'       => $def['mobile'] ?? 'mais',
            ];
        }

        return $itens;
    }

    /**
     * Primeira rota acessível pelo usuário — usada como landing após o login,
     * assim quem só tem um módulo liberado já cai direto nele.
     */
    public static function rotaInicial(User $user): string
    {
        return self::itensPermitidos($user)[0]['route'] ?? 'login';
    }
}
