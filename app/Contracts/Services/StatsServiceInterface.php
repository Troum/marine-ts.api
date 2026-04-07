<?php

namespace App\Contracts\Services;

interface StatsServiceInterface
{
    /**
     * @return array{news_count: int, projects_count: int, featured_news: int, services_count: int, vacancies_count: int, application_forms_count: int}
     */
    public function getAggregates(): array;
}
