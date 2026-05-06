<?php

namespace App\Services;

use App\Contracts\Repositories\SiteSettingRepositoryInterface;
use App\DTO\Navigation\UpdateNavigationSettingsDto;

class NavigationSettingsService
{
    public const string KEY = 'navigation';

    public function __construct(
        private readonly SiteSettingRepositoryInterface $siteSettingRepository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getNavigation(): array
    {
        $value = $this->siteSettingRepository->getValueByKey(self::KEY);
        if ($value !== null) {
            return $this->normalize($value);
        }

        return $this->defaultNavigation();
    }

    /**
     * @return array<string, mixed>
     */
    public function updateNavigation(UpdateNavigationSettingsDto $dto): array
    {
        $normalized = $this->normalize([
            'main' => $dto->main,
            'more' => $dto->more,
            'menuVariant' => $dto->menuVariant,
            'menuFontSize' => $dto->menuFontSize,
            'menuFontWeight' => $dto->menuFontWeight,
            'menuTextCase' => $dto->menuTextCase,
            'menuJustify' => $dto->menuJustify,
            'menuItemHoverColor' => $dto->menuItemHoverColor,
            'menuItemColor' => $dto->menuItemColor,
            'horizItems' => $dto->horizItems,
            'burgerContacts' => $dto->burgerContacts,
        ]);
        $this->siteSettingRepository->updateOrCreateValue(self::KEY, $normalized);

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $value
     * @return array<string, mixed>
     */
    public function normalize(array $value): array
    {
        $defaults = $this->defaultNavigation();
        $main = $value['main'] ?? $defaults['main'];
        $more = $value['more'] ?? $defaults['more'];
        if (! is_array($main)) {
            $main = $defaults['main'];
        }
        if (! is_array($more)) {
            $more = $defaults['more'];
        }

        return [
            'main' => array_values(array_map(fn ($row) => $this->normalizeItem($row), $main)),
            'more' => array_values(array_map(fn ($row) => $this->normalizeItem($row), $more)),
            'menuVariant' => $this->normalizeMenuVariant($value['menuVariant'] ?? $value['menu_variant'] ?? null),
            'menuFontSize' => $this->normalizeMenuFontSize($value['menuFontSize'] ?? $value['menu_font_size'] ?? null),
            'menuFontWeight' => $this->normalizeMenuFontWeight($value['menuFontWeight'] ?? $value['menu_font_weight'] ?? null),
            'menuTextCase' => $this->normalizeMenuTextCase($value['menuTextCase'] ?? $value['menu_text_case'] ?? null),
            'menuJustify' => $this->normalizeMenuJustify($value['menuJustify'] ?? $value['menu_justify'] ?? null),
        ] + $this->normalizeOptionalExtras($value);
    }

    /**
     * @param  array<string, mixed>  $value
     * @return array<string, mixed>
     */
    private function normalizeOptionalExtras(array $value): array
    {
        $out = [];
        $hover = $this->normalizeOptionalColor($value['menuItemHoverColor'] ?? $value['menu_item_hover_color'] ?? null);
        if ($hover !== null) {
            $out['menuItemHoverColor'] = $hover;
        }
        $color = $this->normalizeOptionalColor($value['menuItemColor'] ?? $value['menu_item_color'] ?? null);
        if ($color !== null) {
            $out['menuItemColor'] = $color;
        }
        $horizRaw = $value['horizItems'] ?? $value['horiz_items'] ?? null;
        if (is_array($horizRaw) && $horizRaw !== []) {
            $horiz = array_values(array_map(fn ($row) => $this->normalizeItem($row), $horizRaw));
            if ($horiz !== []) {
                $out['horizItems'] = $horiz;
            }
        }
        $bc = $this->normalizeBurgerContacts($value['burgerContacts'] ?? $value['burger_contacts'] ?? null);
        if ($bc !== null) {
            $out['burgerContacts'] = $bc;
        }

        return $out;
    }

    private function normalizeOptionalColor(mixed $raw): ?string
    {
        if (! is_string($raw)) {
            return null;
        }
        $s = trim($raw);
        if ($s === '' || strlen($s) > 32) {
            return null;
        }

        return $s;
    }

    /**
     * Значения из вложенного burgerContacts: фронт шлёт camelCase, DTO может отдать snake_case.
     *
     * @param  array<string, mixed>  $assoc
     */
    private function burgerAssocString(array $assoc, string $camel, string $snake): ?string
    {
        foreach ([$camel, $snake] as $key) {
            if (! array_key_exists($key, $assoc)) {
                continue;
            }
            $v = $assoc[$key];
            if ($v === null) {
                continue;
            }
            if (is_string($v)) {
                $t = trim($v);
            } elseif (is_int($v) || is_float($v)) {
                $t = trim((string) $v);
            } else {
                continue;
            }
            if ($t !== '') {
                return $t;
            }
        }

        return null;
    }

    private function cleanUrlString(string $raw): string
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags($raw)) ?? '');
    }

    /**
     * @return string|array{ru: string, en: string}|null
     */
    private function burgerAssocLocalized(array $assoc, string $camel, string $snake): string|array|null
    {
        foreach ([$camel, $snake] as $key) {
            if (! array_key_exists($key, $assoc)) {
                continue;
            }
            $v = $assoc[$key];
            if (is_array($v)) {
                $ru = isset($v['ru']) ? trim((string) $v['ru']) : '';
                $en = isset($v['en']) ? trim((string) $v['en']) : '';
                if ($ru !== '' || $en !== '') {
                    return ['ru' => $ru, 'en' => $en];
                }

                continue;
            }
            $s = $this->burgerAssocString($assoc, $camel, $snake);
            if ($s !== null) {
                return $s;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function normalizeBurgerContacts(mixed $raw): ?array
    {
        if (! is_array($raw)) {
            return null;
        }
        $out = [];
        $phonesTitle = $this->burgerAssocLocalized($raw, 'phonesTitle', 'phones_title');
        if ($phonesTitle !== null) {
            $out['phonesTitle'] = $phonesTitle;
        }
        if (isset($raw['phones']) && is_array($raw['phones'])) {
            $phones = [];
            foreach ($raw['phones'] as $p) {
                $s = trim((string) $p);
                if ($s !== '') {
                    $phones[] = $s;
                }
            }
            if ($phones !== []) {
                $out['phones'] = $phones;
            }
        }
        $emailTitle = $this->burgerAssocLocalized($raw, 'emailTitle', 'email_title');
        if ($emailTitle !== null) {
            $out['emailTitle'] = $emailTitle;
        }

        $emails = [];
        if (isset($raw['emails']) && is_array($raw['emails'])) {
            foreach ($raw['emails'] as $e) {
                $s = trim((string) $e);
                if ($s !== '') {
                    $emails[] = $s;
                }
            }
        }
        if ($emails === []) {
            $legacyEmail = $this->burgerAssocString($raw, 'email', 'email');
            if ($legacyEmail !== null) {
                $emails = [$legacyEmail];
            }
        }
        if ($emails !== []) {
            $out['emails'] = $emails;
        }

        $socials = [];
        if (isset($raw['socials']) && is_array($raw['socials'])) {
            foreach ($raw['socials'] as $soc) {
                if (! is_array($soc)) {
                    continue;
                }
                $url = $this->burgerAssocString($soc, 'url', 'url');
                $url = $url !== null ? $this->cleanUrlString($url) : null;
                if ($url === null) {
                    continue;
                }
                $label = $this->burgerAssocString($soc, 'label', 'label') ?? $url;
                $socials[] = ['url' => $url, 'label' => $label];
            }
        }
        if ($socials === []) {
            $url = $this->burgerAssocString($raw, 'socialUrl', 'social_url');
            $url = $url !== null ? $this->cleanUrlString($url) : null;
            if ($url !== null) {
                $label = $this->burgerAssocString($raw, 'socialLabel', 'social_label') ?? $url;
                $socials[] = ['url' => $url, 'label' => $label];
            }
        }
        if ($socials !== []) {
            $out['socials'] = $socials;
        }

        $officesColumnTitle = $this->burgerAssocLocalized($raw, 'officesColumnTitle', 'offices_column_title');
        if ($officesColumnTitle !== null) {
            $out['officesColumnTitle'] = $officesColumnTitle;
        }

        $offices = [];
        if (isset($raw['offices']) && is_array($raw['offices'])) {
            foreach ($raw['offices'] as $of) {
                if (! is_array($of)) {
                    continue;
                }
                $addr = $this->burgerAssocLocalized($of, 'address', 'address');
                if ($addr === null) {
                    continue;
                }
                $entry = ['address' => $addr];
                $otitle = $this->burgerAssocLocalized($of, 'title', 'title');
                if ($otitle !== null) {
                    $entry['title'] = $otitle;
                }
                $offices[] = $entry;
            }
        }
        if ($offices === []) {
            $legacyAddr = $this->burgerAssocString($raw, 'officeAddress', 'office_address');
            if ($legacyAddr !== null) {
                $offices[] = ['address' => $legacyAddr];
                if (! isset($out['officesColumnTitle'])) {
                    $legacyOfficeTitle = $this->burgerAssocString($raw, 'officeTitle', 'office_title');
                    if ($legacyOfficeTitle !== null) {
                        $out['officesColumnTitle'] = $legacyOfficeTitle;
                    }
                }
            }
        }
        if ($offices !== []) {
            $out['offices'] = $offices;
        }

        return $out === [] ? null : $out;
    }

    private function normalizeMenuVariant(mixed $raw): string
    {
        $v = is_string($raw) ? strtolower(trim($raw)) : '';

        return $v === 'horizontal' ? 'horizontal' : 'overlay';
    }

    private function normalizeMenuFontSize(mixed $raw): string
    {
        $v = is_string($raw) ? strtolower(trim($raw)) : '';
        $allowed = ['sm', 'base', 'lg', 'xl', '2xl'];

        return in_array($v, $allowed, true) ? $v : 'base';
    }

    private function normalizeMenuFontWeight(mixed $raw): string
    {
        $v = is_string($raw) ? strtolower(trim($raw)) : '';
        $allowed = ['light', 'normal', 'medium', 'semibold', 'bold'];

        return in_array($v, $allowed, true) ? $v : 'medium';
    }

    private function normalizeMenuTextCase(mixed $raw): string
    {
        $v = is_string($raw) ? strtolower(trim($raw)) : '';
        $allowed = ['none', 'lowercase', 'uppercase', 'capitalize'];

        return in_array($v, $allowed, true) ? $v : 'none';
    }

    private function normalizeMenuJustify(mixed $raw): string
    {
        $v = is_string($raw) ? strtolower(trim($raw)) : '';

        return $v === 'center' ? 'center' : 'between';
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeItem(mixed $row, bool $allowChildren = true): array
    {
        if (! is_array($row)) {
            return ['path' => '/', 'label' => ['ru' => '', 'en' => '']];
        }
        $path = $this->normalizeNavPath(isset($row['path']) ? (string) $row['path'] : '');
        $label = $row['label'] ?? [];
        $ru = is_array($label) ? (string) ($label['ru'] ?? '') : '';
        $en = is_array($label) ? (string) ($label['en'] ?? '') : '';

        $out = [
            'path' => $path,
            'label' => ['ru' => $ru, 'en' => $en],
        ];

        if ($allowChildren && ! empty($row['children']) && is_array($row['children'])) {
            $children = array_values(array_map(
                fn ($child) => $this->normalizeItem($child, false),
                $row['children'],
            ));
            if ($children !== []) {
                $out['children'] = $children;
            }
        }

        return $out;
    }

    private function normalizeNavPath(string $raw): string
    {
        $path = trim($raw);
        if ($path === '' || $path === '/') {
            return '/';
        }
        if ($path === '#') {
            return '#';
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        if (! str_starts_with($path, '/')) {
            return '/'.$path;
        }

        return $path;
    }

    /**
     * @return array{
     *     main: list<array<string, mixed>>,
     *     more: list<array<string, mixed>>,
     *     menuVariant: string,
     *     menuFontSize: string,
     *     menuFontWeight: string,
     *     menuTextCase: string,
     *     menuJustify: string,
     * }
     */
    public function defaultNavigation(): array
    {
        return [
            'main' => [],
            'more' => [],
            'menuVariant' => 'overlay',
            'menuFontSize' => 'base',
            'menuFontWeight' => 'medium',
            'menuTextCase' => 'none',
            'menuJustify' => 'between',
        ];
    }
}
