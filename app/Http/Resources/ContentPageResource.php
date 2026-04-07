<?php

namespace App\Http\Resources;

use App\Models\ContentPage;
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
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'isPublished' => $this->is_published,
            'sortOrder' => $this->sort_order,
            'seoTitle' => $this->seo_title,
            'seoDescription' => $this->seo_description,
            'seoKeywords' => $this->seo_keywords,
            'contentableType' => ContentableMorph::shortFromClass($this->contentable_type),
            'contentableId' => $this->contentable_type !== null ? $this->contentable_id : null,
            'contentableTitle' => $this->whenLoaded('contentable', fn () => $this->contentable?->title),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
