<?php

namespace App\Contracts\Services;

use App\Models\PageInquiry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PageInquiryServiceInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, ?string $ip): PageInquiry;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateManage(int $perPage, int $page, array $filters): LengthAwarePaginator;

    public function markReadIfUnread(PageInquiry $inquiry): PageInquiry;

    public function delete(PageInquiry $inquiry): void;
}
