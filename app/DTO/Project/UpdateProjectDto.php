<?php

namespace App\DTO\Project;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateProjectDto extends BaseDto
{
    #[MapProperty(from: ['type'], required: false)]
    public readonly ?string $type;

    #[MapProperty(from: ['date'], required: false)]
    public readonly ?string $date;

    #[MapProperty(from: ['image'], required: false)]
    public readonly ?string $image;

    /**
     * @var array<string, array<string, mixed>>|null
     */
    #[MapProperty(from: ['translations'], required: false)]
    public readonly ?array $translations;
}
