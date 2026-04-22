<?php

namespace App\Repositories;

use App\Contracts\Repositories\SiteSettingRepositoryInterface;
use App\Models\SiteSetting;

final class SiteSettingRepository implements SiteSettingRepositoryInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function getValueByKey(string $key): ?array
    {
        $row = SiteSetting::query()->where('key', $key)->first();
        if ($row !== null && is_array($row->value)) {
            return $row->value;
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $value
     */
    public function updateOrCreateValue(string $key, array $value): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }
}
