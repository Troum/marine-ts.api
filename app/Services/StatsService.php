<?php

namespace App\Services;

use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Repositories\ServiceRepositoryInterface;
use App\Contracts\Repositories\ApplicationFormRepositoryInterface;
use App\Contracts\Repositories\VacancyRepositoryInterface;
use App\Contracts\Services\StatsServiceInterface;

final class StatsService implements StatsServiceInterface
{
    public function __construct(
        private readonly NewsRepositoryInterface $newsRepository,
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly ServiceRepositoryInterface $serviceRepository,
        private readonly VacancyRepositoryInterface $vacancyRepository,
        private readonly ApplicationFormRepositoryInterface $applicationFormRepository,
    ) {}

    /**
     * @return array{news_count: int, projects_count: int, featured_news: int, services_count: int, vacancies_count: int, application_forms_count: int}
     */
    public function getAggregates(): array
    {
        return [
            'news_count' => $this->newsRepository->count(),
            'projects_count' => $this->projectRepository->count(),
            'featured_news' => $this->newsRepository->countFeatured(),
            'services_count' => $this->serviceRepository->count(),
            'vacancies_count' => $this->vacancyRepository->count(),
            'application_forms_count' => $this->applicationFormRepository->count(),
        ];
    }
}
