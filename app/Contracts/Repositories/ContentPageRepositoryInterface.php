<?php

namespace App\Contracts\Repositories;

use App\Models\ContentPage;
use Illuminate\Support\Collection;

interface ContentPageRepositoryInterface extends BaseRepositoryInterface
{
    public function findPublishedBySlug(string $slug): ?ContentPage;

    /**
     * @return Collection<int, ContentPage>
     */
    public function listPublishedForPublic(): Collection;

    public function detachContentableDuplicates(string $contentableClass, int $contentableId, int $exceptContentPageId): void;

    /**
     * @param  array<string, array<string, mixed>>  $translations
     */
    public function syncContentPageTranslations(ContentPage $page, array $translations): void;
}
