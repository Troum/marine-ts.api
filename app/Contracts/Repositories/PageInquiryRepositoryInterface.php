<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PageInquiryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateManageList(int $perPage, int $page, array $filters): LengthAwarePaginator;
}
