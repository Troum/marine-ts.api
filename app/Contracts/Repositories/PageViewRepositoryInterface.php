<?php

namespace App\Contracts\Repositories;

interface PageViewRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createOne(array $attributes): void;

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
    public function adminSummary(): array;
}
