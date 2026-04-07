<?php

namespace App\Http\Resources;

use App\Models\ContentPage;
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
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'sortOrder' => $this->sort_order,
        ];
    }
}
