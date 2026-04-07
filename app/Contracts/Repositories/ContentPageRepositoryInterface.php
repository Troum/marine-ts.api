<?php

namespace App\Contracts\Repositories;

use App\Models\ContentPage;

interface ContentPageRepositoryInterface extends BaseRepositoryInterface
{
    public function findPublishedBySlug(string $slug): ?ContentPage;
}
