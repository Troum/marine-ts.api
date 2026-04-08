<?php

namespace App\Http\Resources;

use App\Models\SiteSeoPage;
use App\Models\SiteSeoPageTranslation;
use App\Support\ApiTranslationPayload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SiteSeoPage
 */
class SiteSeoPageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var SiteSeoPage $page */
        $page = $this->resource;
        $locale = app()->getLocale();
        /** @var SiteSeoPageTranslation|null $t */
        $t = $page->translationForLocale($locale);

        $data = [
            'slug' => $this->slug,
            'label' => $t?->label,
            'seoTitle' => $t?->seo_title,
            'seoDescription' => $t?->seo_description,
            'seoKeywords' => $t?->seo_keywords,
        ];

        if (ApiTranslationPayload::wantsFullTranslations($request)) {
            $page->loadMissing('translations');
            $data['translations'] = $this->translationsMap($page);
        }

        return $data;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function translationsMap(SiteSeoPage $page): array
    {
        $out = [];
        foreach ($page->translations as $tr) {
            $out[$tr->locale] = [
                'label' => $tr->label,
                'seoTitle' => $tr->seo_title,
                'seoDescription' => $tr->seo_description,
                'seoKeywords' => $tr->seo_keywords,
            ];
        }

        return $out;
    }
}
