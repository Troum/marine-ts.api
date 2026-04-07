<?php

namespace App\Contracts\Services;

use App\DTO\AdminUser\StoreAdminUserDto;
use App\DTO\AdminUser\UpdateAdminUserDto;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminUserServiceInterface
{
    public function paginate(int $perPage, int $page, array $filters = []): LengthAwarePaginator;

    public function create(StoreAdminUserDto $dto): User;

    public function update(User $user, UpdateAdminUserDto $dto, bool $syncRoles): User;

    public function delete(User $target, User $actor): void;

    /**
     * @return array<int, array{name: string, label: string}>
     */
    public function rolesCatalog(): array;
}
