<?php

namespace App\Http\Resources;

use App\Models\News;
use App\Models\NewsTranslation;
use App\Support\ApiTranslationPayload;
use App\Support\LocalizedDisplayDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin News
 */
class NewsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var News $news */
        $news = $this->resource;
        $locale = app()->getLocale();
        /** @var NewsTranslation|null $t */
        $t = $news->translationForLocale($locale);

        $dateRaw = $this->date;
        $dateOut = ApiTranslationPayload::wantsFullTranslations($request)
            ? $dateRaw
            : LocalizedDisplayDate::format($dateRaw, $locale);

        $data = [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $t?->title,
            'excerpt' => $t?->excerpt,
            'content' => $t?->content,
            'date' => $dateOut,
            'author' => $this->author,
            'category' => $t?->category,
            'featured' => $this->featured,
            'image' => $this->image,
            'seoTitle' => $t?->seo_title,
            'seoDescription' => $t?->seo_description,
            'seoKeywords' => $t?->seo_keywords,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];

        if (ApiTranslationPayload::wantsFullTranslations($request)) {
            $news->loadMissing('translations');
            $data['translations'] = $this->translationsMap($news);
        }

        return $data;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function translationsMap(News $news): array
    {
        $out = [];
        foreach ($news->translations as $tr) {
            $out[$tr->locale] = [
                'title' => $tr->title,
                'excerpt' => $tr->excerpt,
                'content' => $tr->content,
                'category' => $tr->category,
                'seoTitle' => $tr->seo_title,
                'seoDescription' => $tr->seo_description,
                'seoKeywords' => $tr->seo_keywords,
            ];
        }

        return $out;
    }
}
