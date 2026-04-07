<?php

namespace App\Contracts\Services;

use App\DTO\Service\StoreServiceDto;
use App\DTO\Service\UpdateServiceDto;
use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ServiceServiceInterface
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(int $perPage, int $page, array $filters = []): LengthAwarePaginator;

    public function getById(int|string $id): Service;

    public function create(StoreServiceDto $dto): Service;

    public function update(Service $service, UpdateServiceDto $dto): Service;

    public function delete(Service $service, bool $soft = true): void;
}
