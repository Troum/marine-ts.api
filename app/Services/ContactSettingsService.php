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
            'offices' => $dto->offices,
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
        $offices = $value['offices'] ?? $defaults['offices'];
        if (! is_array($quick)) {
            $quick = $defaults['quick'];
        }
        if (! is_array($offices)) {
            $offices = $defaults['offices'];
        }

        return [
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
                    'iconKey' => (string) ($row['iconKey'] ?? 'phone'),
                    'label' => (string) ($row['label'] ?? ''),
                    'value' => (string) ($row['value'] ?? ''),
                    'href' => isset($row['href']) && $row['href'] !== '' ? (string) $row['href'] : null,
                ];
            }, $quick)),
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
                    'city' => (string) ($row['city'] ?? ''),
                    'country' => (string) ($row['country'] ?? ''),
                    'address' => (string) ($row['address'] ?? ''),
                    'phone' => (string) ($row['phone'] ?? ''),
                    'email' => (string) ($row['email'] ?? ''),
                ];
            }, $offices)),
        ];
    }

    /**
     * @return array{quick: list<array{iconKey: string, label: string, value: string, href: string|null}>, offices: list<array{city: string, country: string, address: string, phone: string, email: string}>}
     */
    public function defaultContacts(): array
    {
        return [
            'quick' => [
                ['iconKey' => 'phone', 'label' => 'Телефон', 'value' => '8 (4012) 35-52-90', 'href' => 'tel:84012355290'],
                ['iconKey' => 'mail', 'label' => 'Email', 'value' => 'info@marine-ts.com', 'href' => 'mailto:info@marine-ts.com'],
                ['iconKey' => 'map-pin', 'label' => 'Адрес', 'value' => 'г. Калининград, Россия', 'href' => null],
                ['iconKey' => 'clock', 'label' => 'Режим работы', 'value' => 'Пн-Пт: 9:00 - 18:00', 'href' => null],
                ['iconKey' => 'link', 'label' => 'Соцсеть', 'value' => 'vk.com/marine_ts', 'href' => 'https://vk.com/marine_ts'],
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
