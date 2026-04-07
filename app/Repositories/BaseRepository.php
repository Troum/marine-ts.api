<?php

namespace App\Repositories;

use App\Contracts\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model) {}

    public function index(int $perPage = 15, int $page = 1, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        $query = $this->applyIndexFilters($query, $filters);

        $orderColumn = $filters['order_column'] ?? 'id';
        $orderDir = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderColumn, $orderDir);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    protected function applyIndexFilters(Builder $query, array $filters): Builder
    {
        return $query;
    }

    public function getOne(int|string $id): Model
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    public function createOne(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    public function updateOne(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function deleteBulk(array $ids, bool $soft = true): int
    {
        $deleted = 0;
        foreach ($ids as $id) {
            if ($this->deleteOne($id, $soft)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    public function deleteOne(Model|int|string $modelOrId, bool $soft = true): bool
    {
        $model = $modelOrId instanceof Model
            ? $modelOrId
            : $this->model->newQuery()->findOrFail($modelOrId);

        if ($this->modelUsesSoftDeletes($model)) {
            return $soft ? $model->delete() : $model->forceDelete();
        }

        return (bool) $model->delete();
    }

    public function count(): int
    {
        return $this->model->newQuery()->count();
    }

    protected function modelUsesSoftDeletes(Model $model): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($model), true);
    }
}
