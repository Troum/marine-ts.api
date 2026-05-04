<?php

namespace App\DTO\Appearance;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateAppearanceSettingsDto extends BaseDto
{
    #[MapProperty(from: ['theme'], required: true)]
    public readonly string $theme;

    /**
     * Скрытые разделы публичного сайта: ключ раздела → true (скрыт).
     *
     * @var array<string, bool>|null
     */
    #[MapProperty(from: ['hiddenSections'], required: false)]
    public readonly ?array $hiddenSections;
}
