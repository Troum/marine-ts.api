<?php

namespace App\Services;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AdminUserServiceInterface;
use App\DTO\AdminUser\StoreAdminUserDto;
use App\DTO\AdminUser\UpdateAdminUserDto;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class AdminUserService implements AdminUserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {}

    public function paginate(int $perPage, int $page, array $filters = []): LengthAwarePaginator
    {
        return $this->userRepository->index($perPage, $page, $filters);
    }

    public function create(StoreAdminUserDto $dto): User
    {
        /** @var User $user */
        $user = $this->userRepository->createOne([
            'name' => $dto->name,
            'username' => $dto->username,
            'email' => $dto->email,
            'password' => $dto->password,
        ]);
        $user->syncRoles($dto->roles);

        return $user->fresh(['roles']) ?? $user;
    }

    public function update(User $user, UpdateAdminUserDto $dto, bool $syncRoles): User
    {
        $data = $dto->toArray();
        unset($data['roles']);
        $payload = $this->filterNulls($data);

        if ($payload !== []) {
            $this->userRepository->updateOne($user, $payload);
        }

        if ($syncRoles) {
            $user->syncRoles($dto->roles ?? []);
        }

        return $user->fresh(['roles']) ?? $user;
    }

    public function delete(User $target, User $actor): void
    {
        if ($actor->id === $target->id) {
            throw new AuthorizationException('Нельзя удалить собственную учётную запись.');
        }

        $this->userRepository->deleteOne($target);
    }

    /**
     * @return list<array{name: string, label: string}>
     */
    public function rolesCatalog(): array
    {
        return $this->roleRepository->catalogForGuard('web');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function filterNulls(array $data): array
    {
        return array_filter($data, static fn (mixed $v): bool => $v !== null);
    }
}
