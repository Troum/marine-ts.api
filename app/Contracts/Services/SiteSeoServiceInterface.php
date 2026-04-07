<?php

namespace App\Contracts\Services;

use App\Models\SiteSeoPage;
use Illuminate\Database\Eloquent\Collection;

interface SiteSeoServiceInterface
{
    /**
     * Список SEO-страниц для админки (фильтры из AdminListQuery).
     *
     * @param  array<string, mixed>  $filters
     */
    public function listForAdmin(array $filters): Collection;

    public function getBySlug(string $slug): SiteSeoPage;

    /**
     * @param  array{seoTitle?: string|null, seoDescription?: string|null, seoKeywords?: string|null}  $validated
     */
    public function updateSeo(string $slug, array $validated): SiteSeoPage;
}
