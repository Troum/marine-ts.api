<?php

namespace App\Http\Resources;

use App\Models\ContentPage;
use App\Models\Service;
use App\Models\ServiceTranslation;
use App\Support\ApiTranslationPayload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Service
 */
class ServiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Service $service */
        $service = $this->resource;
        $locale = app()->getLocale();
        /** @var ServiceTranslation|null $t */
        $t = $service->translationForLocale($locale);

        $data = [
            'id' => $this->id,
            'title' => $t?->title,
            'description' => $t?->description,
            'features' => $t?->features ?? [],
            'iconKey' => $this->icon_key,
            'sortOrder' => $this->sort_order,
            'seoTitle' => $t?->seo_title,
            'seoDescription' => $t?->seo_description,
            'seoKeywords' => $t?->seo_keywords,
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
            $service->loadMissing('translations');
            $data['translations'] = $this->translationsMap($service);
        }

        return $data;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function translationsMap(Service $service): array
    {
        $out = [];
        foreach ($service->translations as $tr) {
            $out[$tr->locale] = [
                'title' => $tr->title,
                'description' => $tr->description,
                'features' => $tr->features ?? [],
                'seoTitle' => $tr->seo_title,
                'seoDescription' => $tr->seo_description,
                'seoKeywords' => $tr->seo_keywords,
            ];
        }

        return $out;
    }
}
