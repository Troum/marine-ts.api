<?php

namespace App\DTO\Contact;

use Mycro\Core\Attributes\DefaultValue;
use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateContactSettingsDto extends BaseDto
{
    /**
     * @var list<array<string, mixed>>
     */
    #[MapProperty(from: ['quick'], required: true)]
    public readonly array $quick;

    /**
     * @var list<array<string, mixed>>
     */
    #[MapProperty(from: ['departments'], required: false)]
    #[DefaultValue([])]
    public readonly array $departments;

    /**
     * @var list<array<string, mixed>>
     */
    #[MapProperty(from: ['offices'], required: true)]
    public readonly array $offices;
}
