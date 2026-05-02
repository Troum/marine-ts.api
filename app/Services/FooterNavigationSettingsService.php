<?php

namespace App\Services;

use App\Contracts\Repositories\SiteSettingRepositoryInterface;
use App\DTO\Footer\UpdateFooterNavigationSettingsDto;

class FooterNavigationSettingsService
{
    public const string KEY = 'footer_navigation';

    public function __construct(
        private readonly SiteSettingRepositoryInterface $siteSettingRepository,
    ) {}

    /**
     * @return array{
     *     columns: list<array<string, mixed>>,
     *     legal: list<array<string, mixed>>,
     *     hideFooterGlobally: bool,
     *     hideFooterPaths: list<string>,
     * }
     */
    public function getFooterNavigation(): array
    {
        $value = $this->siteSettingRepository->getValueByKey(self::KEY);
        if ($value !== null) {
            return $this->normalize($value);
        }

        return $this->defaultFooterNavigation();
    }

    /**
     * @return array{
     *     columns: list<array<string, mixed>>,
     *     legal: list<array<string, mixed>>,
     *     hideFooterGlobally: bool,
     *     hideFooterPaths: list<string>,
     * }
     */
    public function updateFooterNavigation(UpdateFooterNavigationSettingsDto $dto): array
    {
        $normalized = $this->normalize([
            'columns' => $dto->columns,
            'legal' => $dto->legal,
            'hideFooterGlobally' => $dto->hideFooterGlobally,
            'hideFooterPaths' => $dto->hideFooterPaths,
        ]);
        $this->siteSettingRepository->updateOrCreateValue(self::KEY, $normalized);

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $value
     * @return array{
     *     columns: list<array<string, mixed>>,
     *     legal: list<array<string, mixed>>,
     *     hideFooterGlobally: bool,
     *     hideFooterPaths: list<string>,
     * }
     */
    public function normalize(array $value): array
    {
        $defaults = $this->defaultFooterNavigation();
        $columns = $value['columns'] ?? $defaults['columns'];
        $legal = $value['legal'] ?? $defaults['legal'];
        if (! is_array($columns)) {
            $columns = $defaults['columns'];
        }
        if (! is_array($legal)) {
            $legal = $defaults['legal'];
        }

        $outColumns = [];
        for ($i = 0; $i < 3; $i++) {
            $col = is_array($columns[$i] ?? null) ? $columns[$i] : [];
            $defCol = $defaults['columns'][$i] ?? ['title' => ['ru' => '', 'en' => ''], 'links' => []];
            $outColumns[] = $this->normalizeColumn($col, $defCol);
        }

        $legalOut = [];
        $legalArr = is_array($legal) ? array_values($legal) : [];
        foreach ($legalArr as $i => $row) {
            $legalOut[] = $this->normalizeLink(is_array($row) ? $row : [], $defaults['legal'][$i] ?? null);
        }

        $hideGlobal = filter_var(
            $value['hideFooterGlobally'] ?? $value['hide_footer_globally'] ?? false,
            FILTER_VALIDATE_BOOL,
        );

        return [
            'columns' => $outColumns,
            'legal' => $legalOut,
            'hideFooterGlobally' => $hideGlobal,
            'hideFooterPaths' => $this->normalizeHideFooterPaths($value['hideFooterPaths'] ?? $value['hide_footer_paths'] ?? []),
        ];
    }

    /**
     * @param  mixed  $raw
     * @return list<string>
     */
    private function normalizeHideFooterPaths(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }
        $paths = [];
        foreach ($raw as $p) {
            if (! is_string($p)) {
                continue;
            }
            $t = trim($p);
            if ($t === '') {
                continue;
            }
            $paths[] = str_starts_with($t, '/') ? $t : '/'.$t;
        }

        return array_values(array_unique($paths));
    }

    /**
     * @param  array<string, mixed>  $col
     * @param  array<string, mixed>  $defaultCol
     * @return array{title: array{ru: string, en: string}, links: list<array<string, mixed>>}
     */
    private function normalizeColumn(array $col, array $defaultCol): array
    {
        $title = $col['title'] ?? [];
        $defTitle = $defaultCol['title'] ?? ['ru' => '', 'en' => ''];
        $titleRu = is_array($title) ? (string) ($title['ru'] ?? $defTitle['ru'] ?? '') : (string) ($defTitle['ru'] ?? '');
        $titleEn = is_array($title) ? (string) ($title['en'] ?? $defTitle['en'] ?? '') : (string) ($defTitle['en'] ?? '');
        $linksRaw = $col['links'] ?? [];
        $links = [];
        if (is_array($linksRaw)) {
            foreach ($linksRaw as $row) {
                $links[] = $this->normalizeLink(is_array($row) ? $row : [], null);
            }
        }

        return [
            'title' => ['ru' => $titleRu, 'en' => $titleEn],
            'links' => array_values($links),
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>|null  $default
     * @return array{path: string, label: array{ru: string, en: string}}
     */
    private function normalizeLink(array $row, ?array $default): array
    {
        if ($default !== null) {
            $path = isset($row['path']) ? (string) $row['path'] : (string) ($default['path'] ?? '/');
            $label = $row['label'] ?? $default['label'] ?? [];
        } else {
            $path = isset($row['path']) ? (string) $row['path'] : '/';
            $label = $row['label'] ?? [];
        }
        $path = $this->normalizeNavPath($path);
        $ru = is_array($label) ? (string) ($label['ru'] ?? '') : '';
        $en = is_array($label) ? (string) ($label['en'] ?? '') : '';

        return [
            'path' => $path,
            'label' => ['ru' => $ru, 'en' => $en],
        ];
    }

    private function normalizeNavPath(string $raw): string
    {
        $path = trim($raw);
        if ($path === '' || $path === '/') {
            return '/';
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
     *     columns: list<array<string, mixed>>,
     *     legal: list<array<string, mixed>>,
     *     hideFooterGlobally: bool,
     *     hideFooterPaths: list<string>,
     * }
     */
    public function defaultFooterNavigation(): array
    {
        return [
            'columns' => [
                [
                    'title' => ['ru' => 'Компания', 'en' => 'Company'],
                    'links' => [
                        ['path' => '/about', 'label' => ['ru' => 'О компании', 'en' => 'About']],
                        ['path' => '/contacts', 'label' => ['ru' => 'Контакты', 'en' => 'Contacts']],
                    ],
                ],
                [
                    'title' => ['ru' => 'Сервисы', 'en' => 'Services'],
                    'links' => [
                        ['path' => '/services', 'label' => ['ru' => 'Сервисы', 'en' => 'Services']],
                        ['path' => '/ship-management', 'label' => ['ru' => 'Судовой менеджмент', 'en' => 'Ship management']],
                        ['path' => '/crewing-management', 'label' => ['ru' => 'Крюинг-менеджмент', 'en' => 'Crew management']],
                    ],
                ],
                [
                    'title' => ['ru' => 'Кандидатам', 'en' => 'For candidates'],
                    'links' => [
                        ['path' => '/vacancies', 'label' => ['ru' => 'Вакансии', 'en' => 'Vacancies']],
                        ['path' => '/application-form', 'label' => ['ru' => 'Анкета', 'en' => 'Application']],
                    ],
                ],
            ],
            'legal' => [
                ['path' => '/privacy', 'label' => ['ru' => 'ПОЛИТИКА КОНФИДЕНЦИАЛЬНОСТИ', 'en' => 'PRIVACY POLICY']],
                ['path' => '/terms', 'label' => ['ru' => 'УСЛОВИЯ ИСПОЛЬЗОВАНИЯ', 'en' => 'TERMS OF USE']],
            ],
            'hideFooterGlobally' => false,
            'hideFooterPaths' => [],
        ];
    }
}
