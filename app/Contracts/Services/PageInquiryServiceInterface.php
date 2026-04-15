<?php

namespace App\Contracts\Services;

use App\DTO\PageInquiry\StorePageInquiryDto;
use App\Models\PageInquiry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PageInquiryServiceInterface
{
    public function store(StorePageInquiryDto $dto, ?string $ip): PageInquiry;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateManage(int $perPage, int $page, array $filters): LengthAwarePaginator;

    public function markReadIfUnread(PageInquiry $inquiry): PageInquiry;

    public function delete(PageInquiry $inquiry): void;
}
