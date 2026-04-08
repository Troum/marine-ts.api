<?php

namespace App\Repositories;

use App\Contracts\Repositories\GalleryItemRepositoryInterface;
use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Collection;

final class GalleryItemRepository extends BaseRepository implements GalleryItemRepositoryInterface
{
    public function __construct(GalleryItem $model)
    {
        parent::__construct($model);
    }

    public function listOrderedWithTranslations(): Collection
    {
        return $this->model->newQuery()
            ->with('translations')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function maxSortOrder(): int
    {
        return (int) $this->model->newQuery()->max('sort_order');
    }
}
