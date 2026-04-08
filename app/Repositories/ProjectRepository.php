<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Models\Project;
use App\Models\ProjectTranslation;
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
        $query = $this->model->newQuery()->with(['contentPage.translations', 'translations']);
        $query = $this->applyIndexFilters($query, $filters);

        $orderColumn = $filters['order_column'] ?? 'id';
        $orderDir = $filters['order_direction'] ?? 'desc';
        $defaultLocale = (string) config('marine.default_locale');

        if ($orderColumn === 'title') {
            $query->orderBy(
                ProjectTranslation::query()
                    ->select('title')
                    ->whereColumn('project_id', 'projects.id')
                    ->where('locale', $defaultLocale)
                    ->limit(1),
                $orderDir
            );
        } elseif ($orderColumn === 'type_label') {
            $query->orderBy(
                ProjectTranslation::query()
                    ->select('type_label')
                    ->whereColumn('project_id', 'projects.id')
                    ->where('locale', $defaultLocale)
                    ->limit(1),
                $orderDir
            );
        } elseif ($orderColumn === 'location') {
            $query->orderBy(
                ProjectTranslation::query()
                    ->select('location')
                    ->whereColumn('project_id', 'projects.id')
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
     * @param  Builder<Project>  $query
     * @return Builder<Project>
     */
    protected function applyIndexFilters(Builder $query, array $filters): Builder
    {
        $defaultLocale = (string) config('marine.default_locale');

        if (! empty($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function (Builder $q) use ($p, $defaultLocale): void {
                $q->where('type', 'like', $p)
                    ->orWhereHas('translations', function (Builder $tq) use ($p, $defaultLocale): void {
                        $tq->where('locale', $defaultLocale)
                            ->where(function (Builder $qq) use ($p): void {
                                $qq->where('title', 'like', $p)
                                    ->orWhere('type_label', 'like', $p)
                                    ->orWhere('location', 'like', $p);
                            });
                    });
            });
        }

        return $query;
    }
}
