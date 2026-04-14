<?php

namespace App\Http\Resources;

use App\Models\ContentPage;
use App\Models\ContentPageTranslation;
use App\Support\ApiTranslationPayload;
use App\Support\ContentableMorph;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ContentPage
 */
class ContentPageResource extends JsonResource
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

        $data = [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $t?->title,
            'excerpt' => $t?->excerpt,
            'body' => $t?->body,
            'isPublished' => $this->is_published,
            'sortOrder' => $this->sort_order,
            'showInquiryForm' => (bool) $this->show_inquiry_form,
            'seoTitle' => $t?->seo_title,
            'seoDescription' => $t?->seo_description,
            'seoKeywords' => $t?->seo_keywords,
            'contentableType' => ContentableMorph::shortFromClass($this->contentable_type),
            'contentableId' => $this->contentable_type !== null ? $this->contentable_id : null,
            'contentableTitle' => $this->whenLoaded('contentable', function () use ($locale) {
                $c = $this->contentable;
                if ($c === null) {
                    return null;
                }

                return $c->translationForLocale($locale)?->title;
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
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
    private function translationsMap(ContentPage $page): array
    {
        $out = [];
        foreach ($page->translations as $tr) {
            $out[$tr->locale] = [
                'title' => $tr->title,
                'excerpt' => $tr->excerpt,
                'body' => $tr->body,
                'seoTitle' => $tr->seo_title,
                'seoDescription' => $tr->seo_description,
                'seoKeywords' => $tr->seo_keywords,
            ];
        }

        return $out;
    }
}
