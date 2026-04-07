<?php

namespace App\Contracts\Services;

interface AnalyticsServiceInterface
{
    public function recordPageView(string $path, ?string $title, ?string $referrer, ?string $ip): void;

    /**
     * @return array{
     *     totalViews: int,
     *     todayViews: int,
     *     last7Days: int,
     *     last30Days: int,
     *     topPaths: list<array{path: string, views: int}>,
     *     dailyLast14Days: list<array{date: string, views: int}>
     * }
     */
    public function getAdminSummary(): array;
}
