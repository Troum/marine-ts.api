<?php

namespace App\Repositories;

use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Models\News;
use App\Support\AdminListQuery;
use Illuminate\Database\Eloquent\Builder;

final class NewsRepository extends BaseRepository implements NewsRepositoryInterface
{
    public function __construct(News $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  Builder<News>  $query
     * @return Builder<News>
     */
    protected function applyIndexFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function (Builder $q) use ($p): void {
                $q->where('title', 'like', $p)
                    ->orWhere('slug', 'like', $p)
                    ->orWhere('category', 'like', $p)
                    ->orWhere('author', 'like', $p);
            });
        }

        return $query;
    }

    public function countFeatured(): int
    {
        return $this->model->newQuery()->where('featured', true)->count();
    }
}
