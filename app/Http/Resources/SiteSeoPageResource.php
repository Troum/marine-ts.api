<?php

namespace App\Http\Resources;

use App\Models\SiteSeoPage;
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
        return [
            'slug' => $this->slug,
            'label' => $this->label,
            'seoTitle' => $this->seo_title,
            'seoDescription' => $this->seo_description,
            'seoKeywords' => $this->seo_keywords,
        ];
    }
}
