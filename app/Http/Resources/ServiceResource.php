<?php

namespace App\Http\Resources;

use App\Models\Service;
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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'features' => $this->features ?? [],
            'iconKey' => $this->icon_key,
            'sortOrder' => $this->sort_order,
            'seoTitle' => $this->seo_title,
            'seoDescription' => $this->seo_description,
            'seoKeywords' => $this->seo_keywords,
            'contentPage' => $this->whenLoaded('contentPage', function () {
                if ($this->contentPage === null) {
                    return null;
                }

                return [
                    'id' => $this->contentPage->id,
                    'slug' => $this->contentPage->slug,
                    'title' => $this->contentPage->title,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
