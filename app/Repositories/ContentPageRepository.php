<?php

namespace App\Repositories;

use App\Contracts\Repositories\ContentPageRepositoryInterface;
use App\Models\ContentPage;
use App\Models\ContentPageTranslation;
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
        $query = $this->model->newQuery()->with(['contentable.translations', 'translations']);
        $query = $this->applyIndexFilters($query, $filters);

        $orderColumn = $filters['order_column'] ?? 'sort_order';
        $orderDir = $filters['order_direction'] ?? 'asc';
        $defaultLocale = (string) config('marine.default_locale');

        if ($orderColumn === 'title') {
            $query->orderBy(
                ContentPageTranslation::query()
                    ->select('title')
                    ->whereColumn('content_page_id', 'content_pages.id')
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
     * @param  Builder<ContentPage>  $query
     * @return Builder<ContentPage>
     */
    protected function applyIndexFilters(Builder $query, array $filters): Builder
    {
        $defaultLocale = (string) config('marine.default_locale');

        if (! empty($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function (Builder $q) use ($p, $defaultLocale): void {
                $q->where('slug', 'like', $p)
                    ->orWhereHas('translations', function (Builder $tq) use ($p, $defaultLocale): void {
                        $tq->where('locale', $defaultLocale)
                            ->where(function (Builder $qq) use ($p): void {
                                $qq->where('title', 'like', $p)
                                    ->orWhere('excerpt', 'like', $p);
                            });
                    });
            });
        }

        if (array_key_exists('published_filter', $filters) && $filters['published_filter'] !== null) {
            $query->where('is_published', $filters['published_filter']);
        }

        if (! empty($filters['exclude_slugs']) && is_array($filters['exclude_slugs'])) {
            $slugs = array_values(array_filter($filters['exclude_slugs'], static fn (mixed $s): bool => is_string($s) && $s !== ''));
            if ($slugs !== []) {
                $query->whereNotIn('slug', $slugs);
            }
        }

        return $query;
    }

    public function findPublishedBySlug(string $slug): ?ContentPage
    {
        return $this->model->newQuery()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->with('translations')
            ->first();
    }
}
