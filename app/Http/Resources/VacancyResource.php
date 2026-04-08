<?php

namespace App\Http\Resources;

use App\Models\Vacancy;
use App\Models\VacancyTranslation;
use App\Support\ApiTranslationPayload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Vacancy
 */
class VacancyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Vacancy $vacancy */
        $vacancy = $this->resource;
        $locale = app()->getLocale();
        /** @var VacancyTranslation|null $t */
        $t = $vacancy->translationForLocale($locale);

        $data = [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $t?->title,
            'excerpt' => $t?->excerpt,
            'content' => $t?->content,
            'requirements' => $t?->requirements ?? [],
            'location' => $t?->location,
            'employmentType' => $t?->employment_type,
            'sortOrder' => $this->sort_order,
            'isPublished' => $this->is_published,
            'seoTitle' => $t?->seo_title,
            'seoDescription' => $t?->seo_description,
            'seoKeywords' => $t?->seo_keywords,
            'applicationFormsCount' => $this->whenCounted('applicationForms'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];

        if (ApiTranslationPayload::wantsFullTranslations($request)) {
            $vacancy->loadMissing('translations');
            $data['translations'] = $this->translationsMap($vacancy);
        }

        return $data;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function translationsMap(Vacancy $vacancy): array
    {
        $out = [];
        foreach ($vacancy->translations as $tr) {
            $out[$tr->locale] = [
                'title' => $tr->title,
                'excerpt' => $tr->excerpt,
                'content' => $tr->content,
                'requirements' => $tr->requirements ?? [],
                'location' => $tr->location,
                'employmentType' => $tr->employment_type,
                'seoTitle' => $tr->seo_title,
                'seoDescription' => $tr->seo_description,
                'seoKeywords' => $tr->seo_keywords,
            ];
        }

        return $out;
    }
}
