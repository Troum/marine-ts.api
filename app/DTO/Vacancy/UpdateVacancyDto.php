<?php

namespace App\DTO\Vacancy;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateVacancyDto extends BaseDto
{
    #[MapProperty(from: ['slug'], required: false)]
    public readonly ?string $slug;

    #[MapProperty(from: ['sortOrder', 'sort_order'], required: false)]
    public readonly ?int $sort_order;

    #[MapProperty(from: ['isPublished', 'is_published'], required: false)]
    public readonly ?bool $is_published;

    /**
     * @var array<string, array<string, mixed>>|null
     */
    #[MapProperty(from: ['translations'], required: false)]
    public readonly ?array $translations;
}
