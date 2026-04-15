<?php

namespace App\Contracts\Services;

use App\DTO\Analytics\RecordPageViewDto;

interface AnalyticsServiceInterface
{
    public function recordPageView(RecordPageViewDto $dto, ?string $ip): void;

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
