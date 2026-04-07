<?php

namespace App\Repositories;

use App\Contracts\Repositories\ServiceRepositoryInterface;
use App\Models\Service;
use App\Support\AdminListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class ServiceRepository extends BaseRepository implements ServiceRepositoryInterface
{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }

    public function index(int $perPage = 15, int $page = 1, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('contentPage');
        $query = $this->applyIndexFilters($query, $filters);

        $allowed = ['id', 'title', 'sort_order', 'icon_key'];
        $col = $filters['order_column'] ?? 'sort_order';
        if (! in_array($col, $allowed, true)) {
            $col = 'sort_order';
        }
        $dir = strtolower((string) ($filters['order_direction'] ?? 'asc'));
        if (! in_array($dir, ['asc', 'desc'], true)) {
            $dir = 'asc';
        }
        $query->orderBy($col, $dir)->orderByDesc('id');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    protected function applyIndexFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function (Builder $q) use ($p): void {
                $q->where('title', 'like', $p)->orWhere('icon_key', 'like', $p);
            });
        }

        return $query;
    }
}
