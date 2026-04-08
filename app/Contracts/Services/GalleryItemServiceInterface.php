<?php

namespace App\Contracts\Services;

use App\DTO\GalleryItem\StoreGalleryItemDto;
use App\DTO\GalleryItem\UpdateGalleryItemDto;
use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

interface GalleryItemServiceInterface
{
    /**
     * @return Collection<int, GalleryItem>
     */
    public function listForManage(): Collection;

    public function create(StoreGalleryItemDto $dto, UploadedFile $image): GalleryItem;

    /**
     * @param  list<string>  $validatedKeys  ключи из $request->validated() — нужны, чтобы отличить «поле не передано» от «передано null» для alt/translations.
     */
    public function update(GalleryItem $item, UpdateGalleryItemDto $dto, array $validatedKeys): GalleryItem;

    public function replaceImage(GalleryItem $item, UploadedFile $image): GalleryItem;

    public function delete(GalleryItem $item): void;
}
