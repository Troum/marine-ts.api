<?php

namespace App\Repositories;

use App\Contracts\Repositories\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;

final class RoleRepository implements RoleRepositoryInterface
{
    /**
     * @return list<array{name: string, label: string}>
     */
    public function catalogForGuard(string $guardName): array
    {
        return Role::query()
            ->where('guard_name', $guardName)
            ->orderBy('name')
            ->get()
            ->map(fn (Role $role): array => [
                'name' => $role->name,
                'label' => $this->roleLabel($role->name),
            ])
            ->values()
            ->all();
    }

    private function roleLabel(string $name): string
    {
        return match ($name) {
            'admin' => 'Администратор',
            'content_manager' => 'Контент-менеджер',
            'hr_manager' => 'HR',
            default => $name,
        };
    }
}
