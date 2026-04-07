<?php

namespace App\Contracts\Repositories;

use App\Models\ApplicationForm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ApplicationFormRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateAll(int $perPage, int $page, array $filters = []): LengthAwarePaginator;

    public function findByDocumentUploadTokenHash(string $hashHex): ?ApplicationForm;
}
