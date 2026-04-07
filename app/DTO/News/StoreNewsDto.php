<?php

namespace App\DTO\News;

use Mycro\Core\Attributes\DefaultValue;
use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreNewsDto extends BaseDto
{
    #[MapProperty(from: ['title'], required: true)]
    public readonly string $title;

    #[MapProperty(from: ['slug'], required: false)]
    public readonly ?string $slug;

    #[MapProperty(from: ['excerpt'], required: true)]
    public readonly string $excerpt;

    #[MapProperty(from: ['content'], required: false)]
    public readonly ?string $content;

    #[MapProperty(from: ['date'], required: true)]
    public readonly string $date;

    #[MapProperty(from: ['author'], required: true)]
    public readonly string $author;

    #[MapProperty(from: ['category'], required: true)]
    public readonly string $category;

    #[MapProperty(from: ['featured'], required: false)]
    #[DefaultValue(false)]
    public readonly bool $featured;

    #[MapProperty(from: ['image'], required: false)]
    public readonly ?string $image;

    #[MapProperty(from: ['seoTitle', 'seo_title'], required: false)]
    public readonly ?string $seo_title;

    #[MapProperty(from: ['seoDescription', 'seo_description'], required: false)]
    public readonly ?string $seo_description;

    #[MapProperty(from: ['seoKeywords', 'seo_keywords'], required: false)]
    public readonly ?string $seo_keywords;
}
