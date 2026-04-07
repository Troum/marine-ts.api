<?php

namespace App\Repositories;

use App\Contracts\Repositories\ApplicationFormRepositoryInterface;
use App\Enums\ApplicationFormStatus;
use App\Models\ApplicationForm;
use App\Support\AdminListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class ApplicationFormRepository extends BaseRepository implements ApplicationFormRepositoryInterface
{
    public function __construct(ApplicationForm $model)
    {
        parent::__construct($model);
    }

    public function paginateAll(int $perPage, int $page, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['vacancy' => static function ($query): void {
                $query->select('id', 'title', 'slug');
            }]);
        $query = $this->applyIndexFilters($query, $filters);

        $allowed = ['id', 'created_at', 'updated_at', 'full_name', 'email', 'status', 'vacancy_id'];
        $col = $filters['order_column'] ?? 'id';
        if (! in_array($col, $allowed, true)) {
            $col = 'id';
        }
        $dir = strtolower((string) ($filters['order_direction'] ?? 'desc'));
        if (! in_array($dir, ['asc', 'desc'], true)) {
            $dir = 'desc';
        }
        $query->orderBy($col, $dir)->orderByDesc('id');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function findByDocumentUploadTokenHash(string $hashHex): ?ApplicationForm
    {
        /** @var ApplicationForm|null */
        return $this->model->newQuery()
            ->where('document_upload_token_hash', $hashHex)
            ->first();
    }

    /**
     * @param  Builder<ApplicationForm>  $query
     * @return Builder<ApplicationForm>
     */
    protected function applyIndexFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['vacancy_id'])) {
            $query->where('vacancy_id', $filters['vacancy_id']);
        }

        if (! empty($filters['status']) && in_array($filters['status'], ApplicationFormStatus::values(), true)) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function (Builder $q) use ($p): void {
                $q->where('full_name', 'like', $p)
                    ->orWhere('email', 'like', $p)
                    ->orWhere('phone', 'like', $p);
            });
        }

        return $query;
    }
}
