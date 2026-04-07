<?php

namespace App\DTO\ContentPage;

use Mycro\Core\Attributes\DefaultValue;
use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreContentPageDto extends BaseDto
{
    #[MapProperty(from: ['slug'], required: true)]
    public readonly string $slug;

    #[MapProperty(from: ['title'], required: true)]
    public readonly string $title;

    #[MapProperty(from: ['excerpt'], required: false)]
    public readonly ?string $excerpt;

    #[MapProperty(from: ['body'], required: true)]
    public readonly string $body;

    #[MapProperty(from: ['isPublished', 'is_published'], required: false)]
    #[DefaultValue(true)]
    public readonly bool $is_published;

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
