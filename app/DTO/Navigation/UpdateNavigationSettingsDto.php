<?php

namespace App\DTO\Navigation;

use Mycro\Core\Attributes\DefaultValue;
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

    #[MapProperty(from: ['menuVariant', 'menu_variant'], required: false)]
    #[DefaultValue('overlay')]
    public readonly string $menuVariant;

    #[MapProperty(from: ['menuFontSize', 'menu_font_size'], required: false)]
    #[DefaultValue('base')]
    public readonly string $menuFontSize;

    #[MapProperty(from: ['menuFontWeight', 'menu_font_weight'], required: false)]
    #[DefaultValue('medium')]
    public readonly string $menuFontWeight;

    #[MapProperty(from: ['menuTextCase', 'menu_text_case'], required: false)]
    #[DefaultValue('none')]
    public readonly string $menuTextCase;

    #[MapProperty(from: ['menuJustify', 'menu_justify'], required: false)]
    #[DefaultValue('between')]
    public readonly string $menuJustify;

    #[MapProperty(from: ['menuItemHoverColor', 'menu_item_hover_color'], required: false)]
    public readonly ?string $menuItemHoverColor;

    #[MapProperty(from: ['menuItemColor', 'menu_item_color'], required: false)]
    public readonly ?string $menuItemColor;

    /**
     * @var list<array<string, mixed>>|null
     */
    #[MapProperty(from: ['horizItems', 'horiz_items'], required: false)]
    public readonly ?array $horizItems;

    /**
     * @var array<string, mixed>|null
     */
    #[MapProperty(from: ['burgerContacts', 'burger_contacts'], required: false)]
    public readonly ?array $burgerContacts;
}
