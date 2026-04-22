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
     * @return array{main: list<array<string, mixed>>, more: list<array<string, mixed>>}
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
     * @return array{main: list<array<string, mixed>>, more: list<array<string, mixed>>}
     */
    public function updateNavigation(UpdateNavigationSettingsDto $dto): array
    {
        $normalized = $this->normalize([
            'main' => $dto->main,
            'more' => $dto->more,
        ]);
        $this->siteSettingRepository->updateOrCreateValue(self::KEY, $normalized);

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $value
     * @return array{main: list<array<string, mixed>>, more: list<array<string, mixed>>}
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
        ];
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
     * @return array{main: list<array<string, mixed>>, more: list<array<string, mixed>>}
     */
    public function defaultNavigation(): array
    {
        return [
            'main' => [],
            'more' => [],
        ];
    }
}
