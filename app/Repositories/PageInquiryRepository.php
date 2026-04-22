<?php

namespace App\Repositories;

use App\Contracts\Repositories\PageInquiryRepositoryInterface;
use App\Models\PageInquiry;
use App\Support\AdminListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class PageInquiryRepository extends BaseRepository implements PageInquiryRepositoryInterface
{
    public function __construct(PageInquiry $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateManageList(int $perPage, int $page, array $filters): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (! empty($filters['search']) && is_string($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function ($q) use ($p): void {
                $q->where('name', 'like', $p)
                    ->orWhere('email', 'like', $p)
                    ->orWhere('company', 'like', $p)
                    ->orWhere('position', 'like', $p)
                    ->orWhere('phone', 'like', $p)
                    ->orWhere('vessel_flag', 'like', $p)
                    ->orWhere('main_ports', 'like', $p)
                    ->orWhere('message', 'like', $p)
                    ->orWhere('source_page', 'like', $p);
            });
        }

        $read = $filters['read'] ?? null;
        if ($read === true) {
            $query->whereNotNull('read_at');
        } elseif ($read === false) {
            $query->whereNull('read_at');
        }

        $col = $filters['order_column'] ?? 'id';
        $dir = $filters['order_direction'] ?? 'desc';
        if (! is_string($col) || ! in_array($col, ['id', 'created_at', 'updated_at', 'read_at'], true)) {
            $col = 'id';
        }
        $dir = is_string($dir) && in_array(strtolower($dir), ['asc', 'desc'], true) ? strtolower($dir) : 'desc';
        $query->orderBy($col, $dir);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
