<?php

namespace App\DTO\GalleryItem;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreGalleryItemDto extends BaseDto
{
    #[MapProperty(from: ['sortOrder', 'sort_order'], required: false)]
    public readonly ?int $sort_order;

    #[MapProperty(from: ['alt'], required: false)]
    public readonly ?string $alt;

    /**
     * @var array<string, array<string, mixed>>|null
     */
    #[MapProperty(from: ['translations'], required: false)]
    public readonly ?array $translations;
}
