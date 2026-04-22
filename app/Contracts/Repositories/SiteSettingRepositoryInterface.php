<?php

namespace App\Contracts\Repositories;

interface SiteSettingRepositoryInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function getValueByKey(string $key): ?array;

    /**
     * @param  array<string, mixed>  $value
     */
    public function updateOrCreateValue(string $key, array $value): void;
}
