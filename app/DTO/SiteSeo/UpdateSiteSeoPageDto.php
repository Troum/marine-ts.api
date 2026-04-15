<?php

namespace App\DTO\SiteSeo;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateSiteSeoPageDto extends BaseDto
{
    /**
     * @var array<string, array<string, mixed>>|null
     */
    #[MapProperty(from: ['translations'], required: false)]
    public readonly ?array $translations;
}
