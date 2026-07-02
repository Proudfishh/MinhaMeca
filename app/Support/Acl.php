<?php

namespace App\Support;

class Acl
{
    /**
     * Resolve um preset de config('acl.roles') para a lista de nomes de permissão.
     * '*' no preset inteiro = todas as permissões. '*' num módulo = todas as ações dele.
     */
    public static function permissoesDoPreset(string|array $preset): array
    {
        $modulos = config('acl.modulos');
        $nomes   = [];

        if ($preset === '*') {
            foreach ($modulos as $modulo => $def) {
                foreach ($def['acoes'] as $acao) {
                    $nomes[] = "{$modulo}.{$acao}";
                }
            }

            return $nomes;
        }

        foreach ($preset as $modulo => $acoes) {
            $disponiveis = $modulos[$modulo]['acoes'] ?? [];
            $acoes = $acoes === '*' ? $disponiveis : $acoes;

            foreach ($acoes as $acao) {
                if (in_array($acao, $disponiveis, true)) {
                    $nomes[] = "{$modulo}.{$acao}";
                }
            }
        }

        return $nomes;
    }

    /**
     * Módulos que o preset de um cargo já habilita (ação "ver"). Usado para saber
     * quais módulos ainda "sobram" pra conceder como acesso extra a um usuário.
     */
    public static function modulosComVerDoPreset(string $role): array
    {
        $preset = config("acl.roles.{$role}");

        if ($preset === null) {
            return [];
        }

        $permissoes = self::permissoesDoPreset($preset);

        return collect($permissoes)
            ->filter(fn ($p) => str_ends_with($p, '.ver'))
            ->map(fn ($p) => substr($p, 0, -4))
            ->values()
            ->all();
    }
}
