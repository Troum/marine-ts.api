<?php

namespace App\DTO\Service;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateServiceDto extends BaseDto
{
    #[MapProperty(from: ['iconKey', 'icon_key'], required: false)]
    public readonly ?string $icon_key;

    #[MapProperty(from: ['sortOrder', 'sort_order'], required: false)]
    public readonly ?int $sort_order;

    /**
     * @var array<string, array<string, mixed>>|null
     */
    #[MapProperty(from: ['translations'], required: false)]
    public readonly ?array $translations;

    #[MapProperty(from: ['removeImage', 'remove_image', 'clear_image'], required: false)]
    public readonly ?bool $clear_image;
}
