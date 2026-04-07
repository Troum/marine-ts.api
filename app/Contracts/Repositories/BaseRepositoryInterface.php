<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, array $filters = []): LengthAwarePaginator;

    public function getOne(int|string $id): Model;

    public function createOne(array $data): Model;

    public function updateOne(Model $model, array $data): bool;

    /**
     * @param  array<int|string>  $ids
     */
    public function deleteBulk(array $ids, bool $soft = true): int;

    public function deleteOne(Model|int|string $modelOrId, bool $soft = true): bool;

    public function count(): int;
}
