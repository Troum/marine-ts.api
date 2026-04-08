<?php

namespace App\Repositories;

use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Models\News;
use App\Models\NewsTranslation;
use App\Support\AdminListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class NewsRepository extends BaseRepository implements NewsRepositoryInterface
{
    public function __construct(News $model)
    {
        parent::__construct($model);
    }

    public function index(int $perPage = 15, int $page = 1, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('translations');
        $query = $this->applyIndexFilters($query, $filters);

        $orderColumn = $filters['order_column'] ?? 'id';
        $orderDir = $filters['order_direction'] ?? 'desc';
        $defaultLocale = (string) config('marine.default_locale');

        if ($orderColumn === 'title') {
            $query->orderBy(
                NewsTranslation::query()
                    ->select('title')
                    ->whereColumn('news_id', 'news.id')
                    ->where('locale', $defaultLocale)
                    ->limit(1),
                $orderDir
            );
        } else {
            $query->orderBy($orderColumn, $orderDir);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param  Builder<News>  $query
     * @return Builder<News>
     */
    protected function applyIndexFilters(Builder $query, array $filters): Builder
    {
        $defaultLocale = (string) config('marine.default_locale');

        if (! empty($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function (Builder $q) use ($p, $defaultLocale): void {
                $q->where('slug', 'like', $p)
                    ->orWhere('author', 'like', $p)
                    ->orWhereHas('translations', function (Builder $tq) use ($p, $defaultLocale): void {
                        $tq->where('locale', $defaultLocale)
                            ->where(function (Builder $qq) use ($p): void {
                                $qq->where('title', 'like', $p)
                                    ->orWhere('category', 'like', $p);
                            });
                    });
            });
        }

        return $query;
    }

    public function countFeatured(): int
    {
        return $this->model->newQuery()->where('featured', true)->count();
    }
}
