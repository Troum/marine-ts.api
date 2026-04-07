<?php

namespace App\Contracts\Services;

use App\DTO\ContentPage\StoreContentPageDto;
use App\DTO\ContentPage\UpdateContentPageDto;
use App\Models\ContentPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ContentPageServiceInterface
{
    public function paginateManage(int $perPage, int $page, array $filters = []): LengthAwarePaginator;

    public function getById(int|string $id): ContentPage;

    public function findPublishedBySlug(string $slug): ?ContentPage;

    /**
     * @return Collection<int, ContentPage>
     */
    public function listPublishedForPublic(): Collection;

    public function create(StoreContentPageDto $dto, ?string $contentableShortType = null, ?int $contentableId = null): ContentPage;

    /**
     * @param  bool  $syncContentable  Если true — обновить полиморфную привязку (в т.ч. отвязать при null).
     */
    public function update(
        ContentPage $page,
        UpdateContentPageDto $dto,
        bool $syncContentable = false,
        ?string $contentableShortType = null,
        ?int $contentableId = null,
    ): ContentPage;

    public function delete(ContentPage $page): void;
}
