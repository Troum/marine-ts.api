<?php

namespace App\DTO\Navigation;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateNavigationSettingsDto extends BaseDto
{
    /**
     * @var list<array<string, mixed>>
     */
    #[MapProperty(from: ['main'], required: true)]
    public readonly array $main;

    /**
     * @var list<array<string, mixed>>
     */
    #[MapProperty(from: ['more'], required: true)]
    public readonly array $more;
}
