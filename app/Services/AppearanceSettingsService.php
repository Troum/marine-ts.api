<?php

namespace App\Services;

use App\Contracts\Repositories\SiteSettingRepositoryInterface;
use App\DTO\Appearance\UpdateAppearanceSettingsDto;

class AppearanceSettingsService
{
    public const string KEY = 'appearance';

    public function __construct(
        private readonly SiteSettingRepositoryInterface $siteSettingRepository,
    ) {}

    /**
     * @return array{theme: string}
     */
    public function getAppearance(): array
    {
        $value = $this->siteSettingRepository->getValueByKey(self::KEY);
        if ($value !== null) {
            return $this->normalize($value);
        }

        return $this->defaultAppearance();
    }

    /**
     * @return array{theme: string}
     */
    public function updateAppearance(UpdateAppearanceSettingsDto $dto): array
    {
        $normalized = $this->normalize(['theme' => $dto->theme]);
        $this->siteSettingRepository->updateOrCreateValue(self::KEY, $normalized);

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $value
     * @return array{theme: string}
     */
    public function normalize(array $value): array
    {
        $defaults = $this->defaultAppearance();
        $theme = $value['theme'] ?? $defaults['theme'];
        if (! is_string($theme)) {
            $theme = $defaults['theme'];
        }
        $theme = strtolower(trim($theme));
        if ($theme === 'sepia') {
            $theme = 'scglobal';
        }
        if (! in_array($theme, ['default', 'scglobal'], true)) {
            $theme = 'default';
        }

        return ['theme' => $theme];
    }

    /**
     * @return array{theme: string}
     */
    private function defaultAppearance(): array
    {
        return ['theme' => 'default'];
    }
}
