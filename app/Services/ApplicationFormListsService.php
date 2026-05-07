<?php

namespace App\Services;

use App\Contracts\Repositories\SiteSettingRepositoryInterface;

final class ApplicationFormListsService
{
    public const string KEY = 'application_form_lists';

    /**
     * @var list<string>
     */
    public const DEFAULT_POSITION_OPTIONS = [
        'Master',
        'Chief Officer',
        '2nd Officer',
        '3rd Officer',
        'OOW',
        'Chief Engineer',
        '2nd Engineer',
        '3rd Engineer',
        '4th Engineer',
        'Motorman',
        'Fitter',
        'Welder',
        'Wiper',
        'ETO',
        'Electrician',
        'Refrigeration Engineer',
        'Crane Operator',
        'Backhoe Dredger Operator',
        'Pumpman',
        'Bosun',
        'AB',
        'OS',
        'Deck Cadet',
        'Electrical Cadet',
        'Engineer Cadet',
        'Cook',
        'Steward',
        'Messman',
        'Other',
    ];

    /**
     * @var list<string>
     */
    public const DEFAULT_VESSEL_TYPE_OPTIONS = [
        'Tanker',
        'Offshore',
        'General Cargo',
        'Dry Cargo',
        'Container',
        'Bulker',
        'Reefer',
        'Fishing',
        'RO-RO',
        'Passenger',
        'Research Vessel',
        'Any',
        'Other',
    ];

    public function __construct(
        private readonly SiteSettingRepositoryInterface $siteSettingRepository,
    ) {}

    /**
     * @return array{positionOptions: list<string>, vesselTypeOptions: list<string>}
     */
    public function get(): array
    {
        $raw = $this->siteSettingRepository->getValueByKey(self::KEY);
        $pos = $raw['positionOptions'] ?? null;
        $ves = $raw['vesselTypeOptions'] ?? null;

        return [
            'positionOptions' => $this->normalizeStringList(is_array($pos) ? $pos : null, self::DEFAULT_POSITION_OPTIONS),
            'vesselTypeOptions' => $this->normalizeStringList(is_array($ves) ? $ves : null, self::DEFAULT_VESSEL_TYPE_OPTIONS),
        ];
    }

    /**
     * @param  list<string>  $positionOptions
     * @param  list<string>  $vesselTypeOptions
     */
    public function update(array $positionOptions, array $vesselTypeOptions): void
    {
        $this->siteSettingRepository->updateOrCreateValue(self::KEY, [
            'positionOptions' => array_values($positionOptions),
            'vesselTypeOptions' => array_values($vesselTypeOptions),
        ]);
    }

    /**
     * @param  list<string>|null  $input
     * @param  list<string>  $fallback
     * @return list<string>
     */
    private function normalizeStringList(?array $input, array $fallback): array
    {
        if ($input === null || $input === []) {
            return $fallback;
        }
        $out = [];
        foreach ($input as $item) {
            if (! is_string($item)) {
                continue;
            }
            $t = trim($item);
            if ($t !== '' && ! in_array($t, $out, true)) {
                $out[] = $t;
            }
        }

        return $out !== [] ? $out : $fallback;
    }
}
