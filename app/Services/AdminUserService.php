<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AdminUserServiceInterface;
use App\DTO\AdminUser\StoreAdminUserDto;
use App\DTO\AdminUser\UpdateAdminUserDto;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

final class AdminUserService implements AdminUserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
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

    public function rolesCatalog(): array
    {
        return Role::query()
            ->where('guard_name', 'web')
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function filterNulls(array $data): array
    {
        return array_filter($data, static fn (mixed $v): bool => $v !== null);
    }
}
