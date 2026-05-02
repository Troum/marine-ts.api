<?php

namespace App\DTO\Footer;

use Mycro\Core\Attributes\DefaultValue;
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

    #[MapProperty(from: ['hideFooterGlobally', 'hide_footer_globally'], required: false)]
    #[DefaultValue(false)]
    public readonly bool $hideFooterGlobally;

    /**
     * @var list<string>
     */
    #[MapProperty(from: ['hideFooterPaths', 'hide_footer_paths'], required: false)]
    #[DefaultValue([])]
    public readonly array $hideFooterPaths;
}
