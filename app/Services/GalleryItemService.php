<?php

namespace App\Services;

use App\Contracts\Repositories\GalleryItemRepositoryInterface;
use App\Contracts\Services\GalleryItemServiceInterface;
use App\DTO\GalleryItem\StoreGalleryItemDto;
use App\DTO\GalleryItem\UpdateGalleryItemDto;
use App\Models\GalleryItem;
use App\Support\MarineLocale;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class GalleryItemService implements GalleryItemServiceInterface
{
    public function __construct(
        private readonly GalleryItemRepositoryInterface $galleryItemRepository,
    ) {}

    public function listForManage(): Collection
    {
        return $this->galleryItemRepository->listOrderedWithTranslations();
    }

    public function create(StoreGalleryItemDto $dto, UploadedFile $image): GalleryItem
    {
        $path = $image->store('gallery', 'public');

        $sortOrder = $dto->sort_order;
        if ($sortOrder === null) {
            $sortOrder = $this->galleryItemRepository->maxSortOrder() + 1;
        }

        /** @var GalleryItem $item */
        $item = $this->galleryItemRepository->createOne([
            'path' => $path,
            'sort_order' => $sortOrder,
        ]);

        $this->syncTranslations($item, $dto->translations, $dto->alt);

        return $item->load('translations');
    }

    /**
     * @param  list<string>  $validatedKeys
     */
    public function update(GalleryItem $item, UpdateGalleryItemDto $dto, array $validatedKeys): GalleryItem
    {
        $data = [];
        if (in_array('sortOrder', $validatedKeys, true)) {
            $data['sort_order'] = (int) ($dto->sort_order ?? 0);
        }
        if ($data !== []) {
            $this->galleryItemRepository->updateOne($item, $data);
        }

        $shouldSyncTranslations = (bool) array_intersect(['alt', 'translations'], $validatedKeys);
        if ($shouldSyncTranslations) {
            $this->syncTranslations($item->fresh(), $dto->translations, $dto->alt);
        }

        return $item->fresh()->load('translations');
    }

    public function replaceImage(GalleryItem $item, UploadedFile $image): GalleryItem
    {
        $newPath = $image->store('gallery', 'public');
        $oldPath = $item->path;

        $this->galleryItemRepository->updateOne($item, ['path' => $newPath]);
        $this->deleteStoredFileIfManaged($oldPath);

        return $item->fresh()->load('translations');
    }

    public function delete(GalleryItem $item): void
    {
        $path = $item->path;
        $this->galleryItemRepository->deleteOne($item, false);
        $this->deleteStoredFileIfManaged($path);
    }

    /**
     * @param  array<string, array<string, mixed>>|null  $translations
     */
    private function syncTranslations(GalleryItem $item, ?array $translations, ?string $singleAlt): void
    {
        if (is_array($translations)) {
            foreach ($translations as $locale => $row) {
                if (! is_array($row) || ! MarineLocale::isSupported((string) $locale)) {
                    continue;
                }
                $item->translations()->updateOrCreate(
                    ['locale' => (string) $locale],
                    ['alt' => (string) ($row['alt'] ?? '')]
                );
            }

            return;
        }

        $default = (string) config('marine.default_locale');
        $item->translations()->updateOrCreate(
            ['locale' => $default],
            ['alt' => (string) ($singleAlt ?? '')]
        );
    }

    private function deleteStoredFileIfManaged(string $path): void
    {
        if (str_starts_with($path, '/')) {
            return;
        }
        Storage::disk('public')->delete($path);
    }
}
