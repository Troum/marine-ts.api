<?php

namespace App\DTO\News;

use Mycro\Core\Attributes\DefaultValue;
use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreNewsDto extends BaseDto
{
    #[MapProperty(from: ['slug'], required: false)]
    public readonly ?string $slug;

    #[MapProperty(from: ['date'], required: true)]
    public readonly string $date;

    #[MapProperty(from: ['author'], required: true)]
    public readonly string $author;

    #[MapProperty(from: ['featured'], required: false)]
    #[DefaultValue(false)]
    public readonly bool $featured;

    #[MapProperty(from: ['image'], required: false)]
    public readonly ?string $image;

    /**
     * @var array<string, array<string, mixed>>
     */
    #[MapProperty(from: ['translations'], required: true)]
    public readonly array $translations;
}
