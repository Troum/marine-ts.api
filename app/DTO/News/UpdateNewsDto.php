<?php

namespace App\DTO\News;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateNewsDto extends BaseDto
{
    #[MapProperty(from: ['title'], required: false)]
    public readonly ?string $title;

    #[MapProperty(from: ['slug'], required: false)]
    public readonly ?string $slug;

    #[MapProperty(from: ['excerpt'], required: false)]
    public readonly ?string $excerpt;

    #[MapProperty(from: ['content'], required: false)]
    public readonly ?string $content;

    #[MapProperty(from: ['date'], required: false)]
    public readonly ?string $date;

    #[MapProperty(from: ['author'], required: false)]
    public readonly ?string $author;

    #[MapProperty(from: ['category'], required: false)]
    public readonly ?string $category;

    #[MapProperty(from: ['featured'], required: false)]
    public readonly ?bool $featured;

    #[MapProperty(from: ['image'], required: false)]
    public readonly ?string $image;

    #[MapProperty(from: ['seoTitle', 'seo_title'], required: false)]
    public readonly ?string $seo_title;

    #[MapProperty(from: ['seoDescription', 'seo_description'], required: false)]
    public readonly ?string $seo_description;

    #[MapProperty(from: ['seoKeywords', 'seo_keywords'], required: false)]
    public readonly ?string $seo_keywords;
}
