<?php

namespace App\Http\Resources;

use App\Models\Vacancy;
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
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'requirements' => $this->requirements ?? [],
            'location' => $this->location,
            'employmentType' => $this->employment_type,
            'sortOrder' => $this->sort_order,
            'isPublished' => $this->is_published,
            'seoTitle' => $this->seo_title,
            'seoDescription' => $this->seo_description,
            'seoKeywords' => $this->seo_keywords,
            'applicationFormsCount' => $this->whenCounted('applicationForms'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
