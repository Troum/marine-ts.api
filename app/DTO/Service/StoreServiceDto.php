<?php

namespace App\DTO\Service;

use Mycro\Core\Attributes\DefaultValue;
use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreServiceDto extends BaseDto
{
    #[MapProperty(from: ['title'], required: true)]
    public readonly string $title;

    #[MapProperty(from: ['description'], required: true)]
    public readonly string $description;

    /**
     * @var list<string>|array<int, string>
     */
    #[MapProperty(from: ['features'], required: true)]
    public readonly array $features;

    #[MapProperty(from: ['iconKey', 'icon_key'], required: true)]
    public readonly string $icon_key;

    #[MapProperty(from: ['sortOrder', 'sort_order'], required: false)]
    #[DefaultValue(0)]
    public readonly int $sort_order;

    #[MapProperty(from: ['seoTitle', 'seo_title'], required: false)]
    public readonly ?string $seo_title;

    #[MapProperty(from: ['seoDescription', 'seo_description'], required: false)]
    public readonly ?string $seo_description;

    #[MapProperty(from: ['seoKeywords', 'seo_keywords'], required: false)]
    public readonly ?string $seo_keywords;
}
