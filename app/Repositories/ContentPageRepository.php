<?php

namespace App\Repositories;

use App\Contracts\Repositories\ContentPageRepositoryInterface;
use App\Models\ContentPage;
use App\Support\AdminListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class ContentPageRepository extends BaseRepository implements ContentPageRepositoryInterface
{
    public function __construct(ContentPage $model)
    {
        parent::__construct($model);
    }

    public function index(int $perPage = 15, int $page = 1, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('contentable');

        $query = $this->applyIndexFilters($query, $filters);

        $orderColumn = $filters['order_column'] ?? 'sort_order';
        $orderDir = $filters['order_direction'] ?? 'asc';
        $query->orderBy($orderColumn, $orderDir);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param  Builder<ContentPage>  $query
     * @return Builder<ContentPage>
     */
    protected function applyIndexFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function (Builder $q) use ($p): void {
                $q->where('title', 'like', $p)
                    ->orWhere('slug', 'like', $p)
                    ->orWhere('excerpt', 'like', $p);
            });
        }

        if (array_key_exists('published_filter', $filters) && $filters['published_filter'] !== null) {
            $query->where('is_published', $filters['published_filter']);
        }

        return $query;
    }

    public function findPublishedBySlug(string $slug): ?ContentPage
    {
        return $this->model->newQuery()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();
    }
}
