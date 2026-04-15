<?php

namespace App\DTO\Footer;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateFooterNavigationSettingsDto extends BaseDto
{
    /**
     * @var list<array<string, mixed>>
     */
    #[MapProperty(from: ['columns'], required: true)]
    public readonly array $columns;

    /**
     * @var list<array<string, mixed>>
     */
    #[MapProperty(from: ['legal'], required: true)]
    public readonly array $legal;
}
