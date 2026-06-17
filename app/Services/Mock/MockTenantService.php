<?php

namespace App\Services\Mock;

class MockTenantService
{
    public function all(): array
    {
        return [
            ['id' => 1, 'nome' => 'Auto Center Premium', 'cidade' => 'São Paulo'],
            ['id' => 2, 'nome' => 'Oficina do Zé',       'cidade' => 'Campinas'],
            ['id' => 3, 'nome' => 'MecaRápida Express',  'cidade' => 'Santos'],
        ];
    }

    public function find(int $id): array
    {
        return collect($this->all())->firstWhere('id', $id) ?? $this->all()[0];
    }
}
