<?php

namespace App\Services;

use App\Contracts\Repositories\SiteSettingRepositoryInterface;
use App\DTO\Contact\UpdateContactSettingsDto;

class ContactSettingsService
{
    public const KEY = 'contacts';

    public function __construct(
        private readonly SiteSettingRepositoryInterface $siteSettingRepository,
    ) {}

    /**
     * @return array{quick: list<array{iconKey: string, label: string, value: string, href: string|null}>, offices: list<array{city: string, country: string, address: string, phone: string, email: string}>}
     */
    public function getContacts(): array
    {
        $value = $this->siteSettingRepository->getValueByKey(self::KEY);
        if ($value !== null) {
            return $this->normalize($value);
        }

        return $this->defaultContacts();
    }

    public function updateContacts(UpdateContactSettingsDto $dto): array
    {
        $normalized = $this->normalize([
            'quick' => $dto->quick,
            'departments' => $dto->departments,
            'offices' => $dto->offices,
            'socials' => $dto->socials,
        ]);
        $this->siteSettingRepository->updateOrCreateValue(self::KEY, $normalized);

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $value
     * @return array{quick: list<array{iconKey: string, label: string, value: string, href: string|null}>, offices: list<array{city: string, country: string, address: string, phone: string, email: string}>}
     */
    public function normalize(array $value): array
    {
        $defaults = $this->defaultContacts();
        $quick = $value['quick'] ?? $defaults['quick'];
        $departments = $value['departments'] ?? $defaults['departments'];
        $offices = $value['offices'] ?? $defaults['offices'];
        $socialsRaw = $value['socials'] ?? null;
        if (! is_array($quick)) {
            $quick = $defaults['quick'];
        }
        if (! is_array($departments)) {
            $departments = $defaults['departments'];
        }
        if (! is_array($offices)) {
            $offices = $defaults['offices'];
        }

        $socials = [];
        if (is_array($socialsRaw)) {
            foreach ($socialsRaw as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $iconKey = trim((string) ($row['iconKey'] ?? $row['icon_key'] ?? ''));
                $url = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($row['url'] ?? ''))) ?? '');
                if ($iconKey === '' || $url === '') {
                    continue;
                }
                $socials[] = ['iconKey' => $iconKey, 'url' => $url];
            }
        }

        return [
            'socials' => $socials,
            'quick' => array_values(array_map(function ($row) {
                if (! is_array($row)) {
                    return [
                        'iconKey' => 'phone',
                        'label' => '',
                        'value' => '',
                        'href' => null,
                    ];
                }

                return [
                    'iconKey' => $this->normalizeIconKey($row['iconKey'] ?? $row['icon_key'] ?? $row['icon'] ?? null),
                    'label' => $this->normalizeLocalizedLine($row['label'] ?? ''),
                    'value' => $this->normalizeLocalizedLine($row['value'] ?? ''),
                    'href' => isset($row['href']) && $row['href'] !== '' ? (string) $row['href'] : null,
                    'showInFooter' => $this->boolValue($row['showInFooter'] ?? $row['show_in_footer'] ?? true),
                ];
            }, $quick)),
            'departments' => array_values(array_map(function ($row) {
                if (! is_array($row)) {
                    return [
                        'title' => '',
                        'phone' => '',
                        'email' => '',
                        'showInFooter' => false,
                    ];
                }

                return [
                    'title' => $this->normalizeLocalizedLine($row['title'] ?? ''),
                    'phone' => $this->normalizeLocalizedLine($row['phone'] ?? ''),
                    'email' => (string) ($row['email'] ?? ''),
                    'showInFooter' => $this->boolValue($row['showInFooter'] ?? $row['show_in_footer'] ?? false),
                ];
            }, $departments)),
            'offices' => array_values(array_map(function ($row) {
                if (! is_array($row)) {
                    return [
                        'city' => '',
                        'country' => '',
                        'address' => '',
                        'phone' => '',
                        'email' => '',
                    ];
                }

                return [
                    'city' => $this->normalizeLocalizedLine($row['city'] ?? ''),
                    'country' => $this->normalizeLocalizedLine($row['country'] ?? ''),
                    'address' => $this->normalizeLocalizedLine($row['address'] ?? ''),
                    'phone' => (string) ($row['phone'] ?? ''),
                    'email' => (string) ($row['email'] ?? ''),
                ];
            }, $offices)),
        ];
    }

    /**
     * Строка или { ru, en } — как на фронте (`serializeBilingual` / `normalizeContactSettingsPayload`).
     *
     * @return string|array{ru: string, en: string}
     */
    private function normalizeLocalizedLine(mixed $raw): array|string
    {
        if (is_array($raw)) {
            $ru = trim((string) ($raw['ru'] ?? ''));
            $en = trim((string) ($raw['en'] ?? ''));
            if ($en === '' || $en === $ru) {
                return $ru;
            }

            return ['ru' => $ru, 'en' => $en];
        }

        return trim((string) $raw);
    }

    private function normalizeIconKey(mixed $raw): string
    {
        if (! is_string($raw)) {
            return 'phone';
        }

        $key = str_replace('_', '-', strtolower(trim($raw)));

        return match ($key) {
            'mail', 'email' => 'mail',
            'map-pin', 'mappin', 'address' => 'map-pin',
            'clock', 'time' => 'clock',
            'link', 'external-link' => 'link',
            'linkedin', 'linked-in' => 'linkedin',
            'vk', 'vkontakte' => 'vk',
            'max' => 'max',
            'phone' => 'phone',
            default => 'phone',
        };
    }

    private function boolValue(mixed $raw): bool
    {
        return filter_var($raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }

    /**
     * @return array{quick: list<array{iconKey: string, label: string, value: string, href: string|null}>, offices: list<array{city: string, country: string, address: string, phone: string, email: string}>}
     */
    public function defaultContacts(): array
    {
        return [
            'quick' => [
                ['iconKey' => 'phone', 'label' => 'Телефон', 'value' => '8 (4012) 35-52-90', 'href' => 'tel:84012355290', 'showInFooter' => true],
                ['iconKey' => 'mail', 'label' => 'Email', 'value' => 'info@marine-ts.com', 'href' => 'mailto:info@marine-ts.com', 'showInFooter' => true],
                ['iconKey' => 'map-pin', 'label' => 'Адрес', 'value' => 'г. Калининград, Россия', 'href' => null, 'showInFooter' => true],
                ['iconKey' => 'clock', 'label' => 'Режим работы', 'value' => 'Пн-Пт: 9:00 - 18:00', 'href' => null, 'showInFooter' => false],
                ['iconKey' => 'link', 'label' => 'Соцсеть', 'value' => 'vk.com/marine_ts', 'href' => 'https://vk.com/marine_ts', 'showInFooter' => false],
            ],
            'departments' => [
                ['title' => 'Отдел судового менеджмента', 'phone' => '8 4012 35 52 90 (доб 1)', 'email' => 'sblokhin@marin-ts.com', 'showInFooter' => false],
                ['title' => 'Отдел крюинга', 'phone' => '8 4012 35 52 90 (доб 4)', 'email' => 'cv@marin-ts.com', 'showInFooter' => false],
                ['title' => 'Отдел снабжения', 'phone' => '8 4012 35 52 90 (доб 2)', 'email' => 'snabzheniye@marin-ts.com', 'showInFooter' => false],
                ['title' => 'Отдел судоремонта', 'phone' => '8 4012 35 52 90 (доб 3)', 'email' => 'tech2@marin-ts.com', 'showInFooter' => false],
            ],
            'offices' => [
                [
                    'city' => 'Калининград',
                    'country' => 'Россия',
                    'address' => 'ул. Портовая, 15, офис 302',
                    'phone' => '8 (4012) 35-52-90',
                    'email' => 'info@marine-ts.com',
                ],
                [
                    'city' => 'Дубай',
                    'country' => 'ОАЭ',
                    'address' => 'Dubai Maritime City, Building 45',
                    'phone' => '+971 4 123 4567',
                    'email' => 'dubai@marine-ts.com',
                ],
                [
                    'city' => 'Роттердам',
                    'country' => 'Нидерланды',
                    'address' => 'Wilhelminakade 123, 3072 AP',
                    'phone' => '+31 10 123 4567',
                    'email' => 'rotterdam@marine-ts.com',
                ],
            ],
        ];
    }
}
