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
     * @return array{theme: string, hiddenSections: array<string, bool>}
     */
    public function updateAppearance(UpdateAppearanceSettingsDto $dto): array
    {
        $normalized = $this->normalize([
            'theme' => $dto->theme,
            'hiddenSections' => $dto->hiddenSections ?? [],
        ]);
        $this->siteSettingRepository->updateOrCreateValue(self::KEY, $normalized);

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $value
     * @return array{theme: string, hiddenSections: array<string, bool>}
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

        $rawHidden = $value['hiddenSections'] ?? [];
        $hiddenSections = [];
        if (is_array($rawHidden)) {
            foreach ($rawHidden as $k => $v) {
                if (is_string($k) && preg_match('/^[a-z_]{1,64}$/', $k)) {
                    $hiddenSections[$k] = (bool) $v;
                }
            }
        }

        return ['theme' => $theme, 'hiddenSections' => $hiddenSections];
    }

    /**
     * @return array{theme: string, hiddenSections: array<string, bool>}
     */
    private function defaultAppearance(): array
    {
        return ['theme' => 'default', 'hiddenSections' => []];
    }
}
