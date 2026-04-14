<?php

namespace App\Services;

use App\Models\SiteSetting;

class NavigationSettingsService
{
    public const string KEY = 'navigation';

    /**
     * @return array{main: list<array{path: string, label: array{ru: string, en: string}}>, more: list<array{path: string, label: array{ru: string, en: string}}>}
     */
    public function getNavigation(): array
    {
        $row = SiteSetting::query()->where('key', self::KEY)->first();
        if ($row !== null && is_array($row->value)) {
            return $this->normalize($row->value);
        }

        return $this->defaultNavigation();
    }

    /**
     * @param  array{main?: mixed, more?: mixed}  $data
     * @return array{main: list<array{path: string, label: array{ru: string, en: string}}>, more: list<array{path: string, label: array{ru: string, en: string}}>}
     */
    public function updateNavigation(array $data): array
    {
        $normalized = $this->normalize($data);
        SiteSetting::query()->updateOrCreate(
            ['key' => self::KEY],
            ['value' => $normalized],
        );

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $value
     * @return array{main: list<array{path: string, label: array{ru: string, en: string}}>, more: list<array{path: string, label: array{ru: string, en: string}}>}
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
     * @param  mixed  $row
     * @return array{path: string, label: array{ru: string, en: string}}
     */
    private function normalizeItem(mixed $row): array
    {
        if (! is_array($row)) {
            return ['path' => '/', 'label' => ['ru' => '', 'en' => '']];
        }
        $path = isset($row['path']) ? trim((string) $row['path']) : '/';
        $label = $row['label'] ?? [];
        $ru = is_array($label) ? (string) ($label['ru'] ?? '') : '';
        $en = is_array($label) ? (string) ($label['en'] ?? '') : '';

        return [
            'path' => $path === '' ? '/' : $path,
            'label' => ['ru' => $ru, 'en' => $en],
        ];
    }

    /**
     * @return array{main: list<array{path: string, label: array{ru: string, en: string}}>, more: list<array{path: string, label: array{ru: string, en: string}}>}
     */
    public function defaultNavigation(): array
    {
        /** @var array{main: list<array{path: string, label: array{ru: string, en: string}}>, more: list<array{path: string, label: array{ru: string, en: string}}>} */
        return config('navigations');
    }
}
