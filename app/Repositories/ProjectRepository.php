<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Models\Project;
use App\Support\AdminListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    public function index(int $perPage = 15, int $page = 1, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('contentPage');
        $query = $this->applyIndexFilters($query, $filters);

        $orderColumn = $filters['order_column'] ?? 'id';
        $orderDir = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderColumn, $orderDir);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param  Builder<Project>  $query
     * @return Builder<Project>
     */
    protected function applyIndexFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function (Builder $q) use ($p): void {
                $q->where('title', 'like', $p)
                    ->orWhere('type_label', 'like', $p)
                    ->orWhere('location', 'like', $p)
                    ->orWhere('type', 'like', $p);
            });
        }

        return $query;
    }
}
