<?php

namespace App\Repositories;

use App\Contracts\Repositories\VacancyRepositoryInterface;
use App\Models\Vacancy;
use App\Support\AdminListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class VacancyRepository extends BaseRepository implements VacancyRepositoryInterface
{
    public function __construct(Vacancy $model)
    {
        parent::__construct($model);
    }

    public function index(int $perPage = 15, int $page = 1, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();
        $query = $this->applyIndexFilters($query, $filters);

        $allowed = ['id', 'title', 'slug', 'sort_order', 'is_published', 'location', 'employment_type', 'created_at', 'updated_at'];
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

    public function countPublished(): int
    {
        return $this->model->newQuery()->where('is_published', true)->count();
    }

    /**
     * @param  Builder<Vacancy>  $query
     * @return Builder<Vacancy>
     */
    protected function applyIndexFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['published_only'])) {
            $query->where('is_published', true);
        }

        if (! empty($filters['with_application_forms_count'])) {
            $query->withCount('applicationForms');
        }

        if (array_key_exists('published_filter', $filters)) {
            $query->where('is_published', (bool) $filters['published_filter']);
        }

        if (! empty($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function (Builder $q) use ($p): void {
                $q->where('title', 'like', $p)
                    ->orWhere('slug', 'like', $p)
                    ->orWhere('location', 'like', $p);
            });
        }

        return $query;
    }
}
