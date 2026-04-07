<?php

namespace App\Services;

use App\Contracts\Repositories\ContentPageRepositoryInterface;
use App\Contracts\Services\ContentPageServiceInterface;
use App\DTO\ContentPage\StoreContentPageDto;
use App\DTO\ContentPage\UpdateContentPageDto;
use App\Models\ContentPage;
use App\Support\ContentableMorph;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class ContentPageService implements ContentPageServiceInterface
{
    public function __construct(
        private readonly ContentPageRepositoryInterface $contentPageRepository,
    ) {}

    public function paginateManage(int $perPage, int $page, array $filters = []): LengthAwarePaginator
    {
        return $this->contentPageRepository->index($perPage, $page, $filters);
    }

    public function getById(int|string $id): ContentPage
    {
        /** @var ContentPage */
        return $this->contentPageRepository->getOne($id);
    }

    public function findPublishedBySlug(string $slug): ?ContentPage
    {
        return $this->contentPageRepository->findPublishedBySlug($slug);
    }

    public function listPublishedForPublic(): Collection
    {
        return ContentPage::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }

    public function create(StoreContentPageDto $dto, ?string $contentableShortType = null, ?int $contentableId = null): ContentPage
    {
        /** @var ContentPage */
        $page = $this->contentPageRepository->createOne([
            'slug' => $dto->slug,
            'title' => $dto->title,
            'excerpt' => $dto->excerpt,
            'body' => $dto->body,
            'is_published' => $dto->is_published,
            'sort_order' => $dto->sort_order,
            'seo_title' => $dto->seo_title,
            'seo_description' => $dto->seo_description,
            'seo_keywords' => $dto->seo_keywords,
        ]);

        if ($contentableShortType !== null && $contentableId !== null) {
            $this->syncContentableLink($page, $contentableShortType, $contentableId);
        }

        return $page->fresh()->load('contentable');
    }

    public function update(
        ContentPage $page,
        UpdateContentPageDto $dto,
        bool $syncContentable = false,
        ?string $contentableShortType = null,
        ?int $contentableId = null,
    ): ContentPage {
        $data = $dto->toArray();
        $payload = $this->filterNulls($data);
        if ($payload !== []) {
            $this->contentPageRepository->updateOne($page, $payload);
        }

        if ($syncContentable) {
            if ($contentableShortType !== null && $contentableId !== null) {
                $this->syncContentableLink($page->fresh() ?? $page, $contentableShortType, $contentableId);
            } else {
                $this->unlinkContentable($page->fresh() ?? $page);
            }
        }

        return ($page->fresh() ?? $page)->load('contentable');
    }

    private function unlinkContentable(ContentPage $page): void
    {
        $page->update([
            'contentable_type' => null,
            'contentable_id' => null,
        ]);
    }

    private function syncContentableLink(ContentPage $page, string $shortType, int $id): void
    {
        $class = ContentableMorph::classFromShort($shortType);
        ContentPage::query()
            ->where('contentable_type', $class)
            ->where('contentable_id', $id)
            ->where('id', '!=', $page->id)
            ->update([
                'contentable_type' => null,
                'contentable_id' => null,
            ]);

        $page->update([
            'contentable_type' => $class,
            'contentable_id' => $id,
        ]);
    }

    public function delete(ContentPage $page): void
    {
        $this->contentPageRepository->deleteOne($page);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function filterNulls(array $data): array
    {
        return array_filter($data, static fn (mixed $v): bool => $v !== null);
    }
}
