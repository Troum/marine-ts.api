<?php

namespace App\DTO\Service;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateServiceDto extends BaseDto
{
    #[MapProperty(from: ['title'], required: false)]
    public readonly ?string $title;

    #[MapProperty(from: ['description'], required: false)]
    public readonly ?string $description;

    /**
     * @var list<string>|array<int, string>|null
     */
    #[MapProperty(from: ['features'], required: false)]
    public readonly ?array $features;

    #[MapProperty(from: ['iconKey', 'icon_key'], required: false)]
    public readonly ?string $icon_key;

    #[MapProperty(from: ['sortOrder', 'sort_order'], required: false)]
    public readonly ?int $sort_order;

    #[MapProperty(from: ['seoTitle', 'seo_title'], required: false)]
    public readonly ?string $seo_title;

    #[MapProperty(from: ['seoDescription', 'seo_description'], required: false)]
    public readonly ?string $seo_description;

    #[MapProperty(from: ['seoKeywords', 'seo_keywords'], required: false)]
    public readonly ?string $seo_keywords;
}
