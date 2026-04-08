<?php

namespace App\Http\Resources;

use App\Models\ContentPage;
use App\Models\ContentPageTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Список без тела (Markdown) — для меню и ленты.
 *
 * @mixin ContentPage
 */
class ContentPageSummaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ContentPage $page */
        $page = $this->resource;
        $locale = app()->getLocale();
        /** @var ContentPageTranslation|null $t */
        $t = $page->translationForLocale($locale);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $t?->title,
            'excerpt' => $t?->excerpt,
            'sortOrder' => $this->sort_order,
        ];
    }
}
