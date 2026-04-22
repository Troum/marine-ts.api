<?php

namespace App\Services;

use App\Contracts\Repositories\SiteSeoPageRepositoryInterface;
use App\Contracts\Services\SiteSeoServiceInterface;
use App\DTO\SiteSeo\UpdateSiteSeoPageDto;
use App\Models\SiteSeoPage;
use Illuminate\Database\Eloquent\Collection;

final class SiteSeoService implements SiteSeoServiceInterface
{
    public function __construct(
        private readonly SiteSeoPageRepositoryInterface $siteSeoPageRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, SiteSeoPage>
     */
    public function listForAdmin(array $filters): Collection
    {
        return $this->siteSeoPageRepository->listForAdmin($filters);
    }

    public function getBySlug(string $slug): SiteSeoPage
    {
        return $this->siteSeoPageRepository->getBySlugWithTranslations($slug);
    }

    public function updateSeo(string $slug, UpdateSiteSeoPageDto $dto): SiteSeoPage
    {
        $page = $this->siteSeoPageRepository->getBySlugWithTranslations($slug);

        if ($dto->translations !== null) {
            return $this->siteSeoPageRepository->updateTranslations($page, $dto->translations);
        }

        return $page;
    }
}
