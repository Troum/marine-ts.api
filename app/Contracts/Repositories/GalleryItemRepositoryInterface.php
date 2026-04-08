<?php

namespace App\Contracts\Repositories;

use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Collection;

interface GalleryItemRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Все элементы галереи с переводами, порядок как в админке.
     *
     * @return Collection<int, GalleryItem>
     */
    public function listOrderedWithTranslations(): Collection;

    public function maxSortOrder(): int;
}
