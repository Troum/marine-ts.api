<?php

namespace App\Contracts\Repositories;

use App\Models\News;

interface NewsRepositoryInterface extends BaseRepositoryInterface
{
    public function countFeatured(): int;

    public function findBySlugWithTranslations(string $slug): News;

    /**
     * @param  array<string, array<string, mixed>>  $translations
     */
    public function syncNewsTranslations(News $news, array $translations): void;

    public function uniqueSlugForTitle(string $title): string;
}
