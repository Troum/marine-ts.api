<?php

namespace App\DTO\Vacancy;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateVacancyDto extends BaseDto
{
    #[MapProperty(from: ['title'], required: false)]
    public readonly ?string $title;

    #[MapProperty(from: ['slug'], required: false)]
    public readonly ?string $slug;

    #[MapProperty(from: ['excerpt'], required: false)]
    public readonly ?string $excerpt;

    #[MapProperty(from: ['content'], required: false)]
    public readonly ?string $content;

    /**
     * @var list<string>|array<int, string>|null
     */
    #[MapProperty(from: ['requirements'], required: false)]
    public readonly ?array $requirements;

    #[MapProperty(from: ['location'], required: false)]
    public readonly ?string $location;

    #[MapProperty(from: ['employmentType', 'employment_type'], required: false)]
    public readonly ?string $employment_type;

    #[MapProperty(from: ['sortOrder', 'sort_order'], required: false)]
    public readonly ?int $sort_order;

    #[MapProperty(from: ['isPublished', 'is_published'], required: false)]
    public readonly ?bool $is_published;

    #[MapProperty(from: ['seoTitle', 'seo_title'], required: false)]
    public readonly ?string $seo_title;

    #[MapProperty(from: ['seoDescription', 'seo_description'], required: false)]
    public readonly ?string $seo_description;

    #[MapProperty(from: ['seoKeywords', 'seo_keywords'], required: false)]
    public readonly ?string $seo_keywords;
}
