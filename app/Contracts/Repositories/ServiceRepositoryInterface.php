<?php

namespace App\Contracts\Repositories;

use App\Models\Service;

interface ServiceRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param  array<string, array<string, mixed>>  $translations
     */
    public function syncServiceTranslations(Service $service, array $translations): void;
}
