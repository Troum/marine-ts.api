<?php

namespace App\Services;

use App\Contracts\Services\SiteSeoServiceInterface;
use App\Models\SiteSeoPage;
use App\Support\AdminListQuery;
use Illuminate\Database\Eloquent\Collection;

final class SiteSeoService implements SiteSeoServiceInterface
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function listForAdmin(array $filters): Collection
    {
        $query = SiteSeoPage::query();

        if (! empty($filters['search']) && is_string($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function ($q) use ($p): void {
                $q->where('slug', 'like', $p)->orWhere('label', 'like', $p);
            });
        }

        $col = $filters['order_column'] ?? 'slug';
        $dir = $filters['order_direction'] ?? 'asc';
        if (! is_string($col) || ! in_array($col, ['id', 'slug', 'label'], true)) {
            $col = 'slug';
        }
        $dir = is_string($dir) && in_array(strtolower($dir), ['asc', 'desc'], true) ? strtolower($dir) : 'asc';
        $query->orderBy($col, $dir);

        return $query->get();
    }

    public function getBySlug(string $slug): SiteSeoPage
    {
        /** @var SiteSeoPage */
        return SiteSeoPage::query()->where('slug', $slug)->firstOrFail();
    }

    public function updateSeo(string $slug, array $validated): SiteSeoPage
    {
        $page = SiteSeoPage::query()->where('slug', $slug)->firstOrFail();
        $page->update([
            'seo_title' => $validated['seoTitle'] ?? null,
            'seo_description' => $validated['seoDescription'] ?? null,
            'seo_keywords' => $validated['seoKeywords'] ?? null,
        ]);

        /** @var SiteSeoPage */
        return $page->fresh();
    }
}
