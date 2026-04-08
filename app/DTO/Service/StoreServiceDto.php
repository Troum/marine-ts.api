<?php

namespace App\DTO\Service;

use Mycro\Core\Attributes\DefaultValue;
use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreServiceDto extends BaseDto
{
    #[MapProperty(from: ['iconKey', 'icon_key'], required: true)]
    public readonly string $icon_key;

    #[MapProperty(from: ['sortOrder', 'sort_order'], required: false)]
    #[DefaultValue(0)]
    public readonly int $sort_order;

    /**
     * @var array<string, array<string, mixed>>
     */
    #[MapProperty(from: ['translations'], required: true)]
    public readonly array $translations;
}
