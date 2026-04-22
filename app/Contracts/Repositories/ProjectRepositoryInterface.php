<?php

namespace App\Contracts\Repositories;

use App\Models\Project;

interface ProjectRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param  array<string, array<string, mixed>>  $translations
     */
    public function syncProjectTranslations(Project $project, array $translations): void;
}
