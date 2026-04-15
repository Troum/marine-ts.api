<?php

namespace App\Services;

use App\Contracts\Services\SiteSeoServiceInterface;
use App\DTO\SiteSeo\UpdateSiteSeoPageDto;
use App\Models\SiteSeoPage;
use App\Models\SiteSeoPageTranslation;
use App\Support\AdminListQuery;
use App\Support\MarineLocale;
use App\Support\NormalizeTranslationInput;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class SiteSeoService implements SiteSeoServiceInterface
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function listForAdmin(array $filters): Collection
    {
        $query = SiteSeoPage::query()->with('translations');
        $defaultLocale = (string) config('marine.default_locale');

        if (! empty($filters['search']) && is_string($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function ($q) use ($p, $defaultLocale): void {
                $q->where('slug', 'like', $p)
                    ->orWhereHas('translations', function ($tq) use ($p, $defaultLocale): void {
                        $tq->where('locale', $defaultLocale)
                            ->where('label', 'like', $p);
                    });
            });
        }

        $col = $filters['order_column'] ?? 'slug';
        $dir = $filters['order_direction'] ?? 'asc';
        if (! is_string($col) || ! in_array($col, ['id', 'slug', 'label'], true)) {
            $col = 'slug';
        }
        $dir = is_string($dir) && in_array(strtolower($dir), ['asc', 'desc'], true) ? strtolower($dir) : 'asc';

        if ($col === 'label') {
            $query->orderBy(
                SiteSeoPageTranslation::query()
                    ->select('label')
                    ->whereColumn('site_seo_page_id', 'site_seo_pages.id')
                    ->where('locale', $defaultLocale)
                    ->limit(1),
                $dir
            );
        } else {
            $query->orderBy($col, $dir);
        }

        return $query->get();
    }

    public function getBySlug(string $slug): SiteSeoPage
    {
        /** @var SiteSeoPage */
        return SiteSeoPage::query()->where('slug', $slug)->with('translations')->firstOrFail();
    }

    public function updateSeo(string $slug, UpdateSiteSeoPageDto $dto): SiteSeoPage
    {
        /** @var SiteSeoPage $page */
        $page = SiteSeoPage::query()->where('slug', $slug)->firstOrFail();

        if ($dto->translations !== null) {
            DB::transaction(function () use ($page, $dto): void {
                foreach ($dto->translations as $locale => $row) {
                    if (! is_array($row) || ! MarineLocale::isSupported((string) $locale)) {
                        continue;
                    }
                    $page->translations()->updateOrCreate(
                        ['locale' => (string) $locale],
                        NormalizeTranslationInput::siteSeoLocaleRow($row)
                    );
                }
            });
        }

        /** @var SiteSeoPage */
        return $page->fresh()->load('translations');
    }
}
