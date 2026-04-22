<?php

namespace App\Contracts\Repositories;

interface RoleRepositoryInterface
{
    /**
     * @return list<array{name: string, label: string}>
     */
    public function catalogForGuard(string $guardName): array;
}
