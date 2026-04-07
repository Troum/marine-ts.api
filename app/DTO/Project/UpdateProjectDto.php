<?php

namespace App\DTO\Project;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateProjectDto extends BaseDto
{
    #[MapProperty(from: ['title'], required: false)]
    public readonly ?string $title;

    #[MapProperty(from: ['type'], required: false)]
    public readonly ?string $type;

    #[MapProperty(from: ['typeLabel', 'type_label'], required: false)]
    public readonly ?string $type_label;

    #[MapProperty(from: ['location'], required: false)]
    public readonly ?string $location;

    #[MapProperty(from: ['date'], required: false)]
    public readonly ?string $date;

    #[MapProperty(from: ['description'], required: false)]
    public readonly ?string $description;

    /**
     * @var array<string, string>|array<string, mixed>|null
     */
    #[MapProperty(from: ['stats'], required: false)]
    public readonly ?array $stats;

    #[MapProperty(from: ['image'], required: false)]
    public readonly ?string $image;

    #[MapProperty(from: ['seoTitle', 'seo_title'], required: false)]
    public readonly ?string $seo_title;

    #[MapProperty(from: ['seoDescription', 'seo_description'], required: false)]
    public readonly ?string $seo_description;

    #[MapProperty(from: ['seoKeywords', 'seo_keywords'], required: false)]
    public readonly ?string $seo_keywords;
}
