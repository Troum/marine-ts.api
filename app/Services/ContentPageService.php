<?php

namespace App\Services;

use App\Contracts\Repositories\ContentPageRepositoryInterface;
use App\Contracts\Services\ContentPageServiceInterface;
use App\DTO\ContentPage\StoreContentPageDto;
use App\DTO\ContentPage\UpdateContentPageDto;
use App\Models\ContentPage;
use App\Support\ContentableMorph;
use App\Support\MarineLocale;
use App\Support\NormalizeTranslationInput;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        $page = $this->contentPageRepository->getOne($id);
        $page->load('translations');
        if ($page->contentable !== null) {
            $page->contentable->load('translations');
        }

        return $page->load('contentable');
    }

    public function findPublishedBySlug(string $slug): ?ContentPage
    {
        $page = $this->contentPageRepository->findPublishedBySlug($slug);
        if ($page === null) {
            return null;
        }
        $page->load('translations');
        if ($page->contentable !== null) {
            $page->contentable->load('translations');
        }

        return $page;
    }

    public function listPublishedForPublic(): Collection
    {
        return ContentPage::query()
            ->where('is_published', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function create(StoreContentPageDto $dto, ?string $contentableShortType = null, ?int $contentableId = null): ContentPage
    {
        $default = (string) config('marine.default_locale');
        if (! isset($dto->translations[$default])) {
            throw new \InvalidArgumentException("translations.$default is required.");
        }

        return DB::transaction(function () use ($dto, $contentableShortType, $contentableId): ContentPage {
            /** @var ContentPage $page */
            $page = $this->contentPageRepository->createOne([
                'slug' => $dto->slug,
                'is_published' => $dto->is_published,
                'sort_order' => $dto->sort_order,
            ]);

            $this->syncContentPageTranslations($page, $dto->translations);

            if ($contentableShortType !== null && $contentableId !== null) {
                $this->syncContentableLink($page->fresh() ?? $page, $contentableShortType, $contentableId);
            }

            return ($page->fresh() ?? $page)->load(['translations', 'contentable.translations']);
        });
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
        unset($payload['translations']);
        if ($payload !== []) {
            $this->contentPageRepository->updateOne($page, $payload);
        }

        if ($dto->translations !== null) {
            DB::transaction(function () use ($page, $dto): void {
                $this->syncContentPageTranslations($page, $dto->translations);
            });
        }

        if ($syncContentable) {
            if ($contentableShortType !== null && $contentableId !== null) {
                $this->syncContentableLink($page->fresh() ?? $page, $contentableShortType, $contentableId);
            } else {
                $this->unlinkContentable($page->fresh() ?? $page);
            }
        }

        $fresh = $page->fresh() ?? $page;
        $fresh->load('translations');
        if ($fresh->contentable !== null) {
            $fresh->contentable->load('translations');
        }

        return $fresh->load('contentable');
    }

    public function delete(ContentPage $page): void
    {
        $this->contentPageRepository->deleteOne($page);
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

    /**
     * @param  array<string, array<string, mixed>>  $translations
     */
    private function syncContentPageTranslations(ContentPage $page, array $translations): void
    {
        foreach (config('marine.locales') as $locale) {
            if (! isset($translations[$locale])) {
                continue;
            }
            if (! MarineLocale::isSupported((string) $locale)) {
                continue;
            }
            $row = NormalizeTranslationInput::contentPageLocaleRow($translations[$locale]);
            $page->translations()->updateOrCreate(
                ['locale' => $locale],
                $row
            );
        }
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
