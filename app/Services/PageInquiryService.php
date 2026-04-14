<?php

namespace App\Services;

use App\Contracts\Services\PageInquiryServiceInterface;
use App\Models\PageInquiry;
use App\Support\AdminListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class PageInquiryService implements PageInquiryServiceInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, ?string $ip): PageInquiry
    {
        /** @var PageInquiry */
        return PageInquiry::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'vessel_name' => $data['vessel_name'] ?? null,
            'imo' => $data['imo'] ?? null,
            'message' => $data['message'],
            'source_page' => $data['source_page'],
            'ip' => $ip,
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateManage(int $perPage, int $page, array $filters): LengthAwarePaginator
    {
        $query = PageInquiry::query();

        if (! empty($filters['search']) && is_string($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function ($q) use ($p): void {
                $q->where('name', 'like', $p)
                    ->orWhere('email', 'like', $p)
                    ->orWhere('company', 'like', $p)
                    ->orWhere('vessel_name', 'like', $p)
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

    public function markReadIfUnread(PageInquiry $inquiry): PageInquiry
    {
        if ($inquiry->read_at === null) {
            $inquiry->update(['read_at' => now()]);
        }

        /** @var PageInquiry */
        return $inquiry->fresh();
    }

    public function delete(PageInquiry $inquiry): void
    {
        $inquiry->delete();
    }
}
