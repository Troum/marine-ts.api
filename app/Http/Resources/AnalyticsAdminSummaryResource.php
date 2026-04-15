<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{
 *     totalViews: int,
 *     todayViews: int,
 *     last7Days: int,
 *     last30Days: int,
 *     topPaths: list<array{path: string, views: int}>,
 *     dailyLast14Days: list<array{date: string, views: int}>
 * } $resource
 */
class AnalyticsAdminSummaryResource extends JsonResource
{
    /** Админ-дашборд ожидает поля счётчиков в корне ответа. */
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'totalViews' => $this->resource['totalViews'],
            'todayViews' => $this->resource['todayViews'],
            'last7Days' => $this->resource['last7Days'],
            'last30Days' => $this->resource['last30Days'],
            'topPaths' => $this->resource['topPaths'],
            'dailyLast14Days' => $this->resource['dailyLast14Days'],
        ];
    }
}
