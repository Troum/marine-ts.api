<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{
 *     news_count: int,
 *     projects_count: int,
 *     featured_news: int,
 *     services_count: int,
 *     vacancies_count: int,
 *     application_forms_count: int
 * } $resource
 */
class StatsAggregatesResource extends JsonResource
{
    /** Публичный `/stats` без вложенного `data` — фронт ожидает поля в корне JSON. */
    public static $wrap = null;

    /**
     * @return array<string, int>
     */
    public function toArray(Request $request): array
    {
        return [
            'news_count' => $this->resource['news_count'],
            'projects_count' => $this->resource['projects_count'],
            'featured_news' => $this->resource['featured_news'],
            'services_count' => $this->resource['services_count'],
            'vacancies_count' => $this->resource['vacancies_count'],
            'application_forms_count' => $this->resource['application_forms_count'],
        ];
    }
}
