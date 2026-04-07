<?php

namespace App\Contracts\Services;

use App\DTO\News\StoreNewsDto;
use App\DTO\News\UpdateNewsDto;
use App\Models\News;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NewsServiceInterface
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(int $perPage, int $page, array $filters = []): LengthAwarePaginator;

    public function getById(int|string $id): News;

    public function getBySlug(string $slug): News;

    public function create(StoreNewsDto $dto): News;

    public function update(News $news, UpdateNewsDto $dto): News;

    public function delete(News $news, bool $soft = true): void;
}
