<?php

namespace App\DTO\Vacancy;

use Mycro\Core\Attributes\DefaultValue;
use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreVacancyDto extends BaseDto
{
    #[MapProperty(from: ['slug'], required: false)]
    public readonly ?string $slug;

    #[MapProperty(from: ['sortOrder', 'sort_order'], required: false)]
    #[DefaultValue(0)]
    public readonly int $sort_order;

    #[MapProperty(from: ['isPublished', 'is_published'], required: false)]
    #[DefaultValue(true)]
    public readonly bool $is_published;

    /**
     * @var array<string, array<string, mixed>>
     */
    #[MapProperty(from: ['translations'], required: true)]
    public readonly array $translations;
}
