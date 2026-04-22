<?php

namespace App\Contracts\Repositories;

use App\Models\SiteSeoPage;
use Illuminate\Database\Eloquent\Collection;

interface SiteSeoPageRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, SiteSeoPage>
     */
    public function listForAdmin(array $filters): Collection;

    public function getBySlugWithTranslations(string $slug): SiteSeoPage;

    public function updateTranslations(SiteSeoPage $page, array $translationsByLocale): SiteSeoPage;
}
