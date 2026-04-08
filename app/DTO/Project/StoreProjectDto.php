<?php

namespace App\DTO\Project;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreProjectDto extends BaseDto
{
    #[MapProperty(from: ['type'], required: true)]
    public readonly string $type;

    #[MapProperty(from: ['date'], required: true)]
    public readonly string $date;

    #[MapProperty(from: ['image'], required: false)]
    public readonly ?string $image;

    /**
     * @var array<string, array<string, mixed>>
     */
    #[MapProperty(from: ['translations'], required: true)]
    public readonly array $translations;
}
