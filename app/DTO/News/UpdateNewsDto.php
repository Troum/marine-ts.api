<?php

namespace App\DTO\News;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateNewsDto extends BaseDto
{
    #[MapProperty(from: ['slug'], required: false)]
    public readonly ?string $slug;

    #[MapProperty(from: ['date'], required: false)]
    public readonly ?string $date;

    #[MapProperty(from: ['author'], required: false)]
    public readonly ?string $author;

    #[MapProperty(from: ['featured'], required: false)]
    public readonly ?bool $featured;

    #[MapProperty(from: ['image'], required: false)]
    public readonly ?string $image;

    /**
     * @var array<string, array<string, mixed>>|null
     */
    #[MapProperty(from: ['translations'], required: false)]
    public readonly ?array $translations;
}
