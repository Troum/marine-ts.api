<?php

namespace App\Http\Resources;

use App\Models\ContentPage;
use App\Models\Project;
use App\Models\ProjectTranslation;
use App\Support\ApiTranslationPayload;
use App\Support\LocalizedDisplayDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
class ProjectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Project $project */
        $project = $this->resource;
        $locale = app()->getLocale();
        /** @var ProjectTranslation|null $t */
        $t = $project->translationForLocale($locale);

        $dateRaw = $this->date;
        $dateOut = ApiTranslationPayload::wantsFullTranslations($request)
            ? $dateRaw
            : LocalizedDisplayDate::format($dateRaw, $locale);

        $data = [
            'id' => $this->id,
            'title' => $t?->title,
            'type' => $this->type,
            'typeLabel' => $t?->type_label,
            'location' => $t?->location,
            'date' => $dateOut,
            'description' => $t?->description,
            'stats' => $t?->stats ?? [],
            'image' => $this->image,
            'seoTitle' => $t?->seo_title,
            'seoDescription' => $t?->seo_description,
            'seoKeywords' => $t?->seo_keywords,
            'seoImage' => $t?->seo_image,
            'contentPage' => $this->whenLoaded('contentPage', function () use ($locale) {
                /** @var ContentPage|null $cp */
                $cp = $this->contentPage;
                if ($cp === null) {
                    return null;
                }
                $cpt = $cp->translationForLocale($locale);

                return [
                    'id' => $cp->id,
                    'slug' => $cp->slug,
                    'title' => $cpt?->title,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];

        if (ApiTranslationPayload::wantsFullTranslations($request)) {
            $project->loadMissing('translations');
            $data['translations'] = $this->translationsMap($project);
        }

        return $data;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function translationsMap(Project $project): array
    {
        $out = [];
        foreach ($project->translations as $tr) {
            $out[$tr->locale] = [
                'title' => $tr->title,
                'typeLabel' => $tr->type_label,
                'location' => $tr->location,
                'description' => $tr->description,
                'stats' => $tr->stats ?? [],
                'seoTitle' => $tr->seo_title,
                'seoDescription' => $tr->seo_description,
                'seoKeywords' => $tr->seo_keywords,
                'seoImage' => $tr->seo_image,
            ];
        }

        return $out;
    }
}
