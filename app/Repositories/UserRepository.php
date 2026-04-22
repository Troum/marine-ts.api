<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Support\AdminListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function index(int $perPage = 15, int $page = 1, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('roles');

        $query = $this->applyIndexFilters($query, $filters);

        $orderColumn = $filters['order_column'] ?? 'id';
        $orderDir = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderColumn, $orderDir);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    protected function applyIndexFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function (Builder $q) use ($p): void {
                $q->where('name', 'like', $p)
                    ->orWhere('username', 'like', $p)
                    ->orWhere('email', 'like', $p);
            });
        }

        return $query;
    }

    public function findByUsernameForAuth(string $username): ?User
    {
        return $this->model->newQuery()
            ->where('username', $username)
            ->with(['roles', 'permissions'])
            ->first();
    }

    public function revokeAllApiTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    public function createApiToken(User $user, string $name): string
    {
        return $user->createToken($name)->plainTextToken;
    }
}
