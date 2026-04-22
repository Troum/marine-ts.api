<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FeedbackMessageRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateManageList(int $perPage, int $page, array $filters): LengthAwarePaginator;
}
