<?php

namespace App\Contracts\Repositories;

use App\Models\Vacancy;

interface VacancyRepositoryInterface extends BaseRepositoryInterface
{
    public function countPublished(): int;

    public function findPublishedBySlugWithTranslations(string $slug): Vacancy;

    /**
     * @param  array<string, array<string, mixed>>  $translations
     */
    public function syncVacancyTranslations(Vacancy $vacancy, array $translations): void;

    public function uniqueSlugForTitle(string $title): string;
}
