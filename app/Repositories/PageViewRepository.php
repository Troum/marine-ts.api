<?php

namespace App\Repositories;

use App\Contracts\Repositories\PageViewRepositoryInterface;
use App\Models\PageView;
use Illuminate\Support\Facades\DB;

final class PageViewRepository implements PageViewRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createOne(array $attributes): void
    {
        PageView::query()->create($attributes);
    }

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
    public function adminSummary(): array
    {
        $now = now();

        $totalViews = PageView::query()->count();
        $todayViews = PageView::query()->whereDate('created_at', $now->toDateString())->count();
        $last7Days = PageView::query()
            ->where('created_at', '>=', $now->copy()->subDays(7)->startOfDay())
            ->count();
        $last30Days = PageView::query()
            ->where('created_at', '>=', $now->copy()->subDays(30)->startOfDay())
            ->count();

        $topRows = PageView::query()
            ->select('path', DB::raw('COUNT(*) as views'))
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $topPaths = $topRows->map(fn ($row) => [
            'path' => $row->path,
            'views' => (int) $row->views,
        ])->values()->all();

        $dailyLast14Days = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i);
            $dailyLast14Days[] = [
                'date' => $d->toDateString(),
                'views' => PageView::query()->whereDate('created_at', $d->toDateString())->count(),
            ];
        }

        return [
            'totalViews' => $totalViews,
            'todayViews' => $todayViews,
            'last7Days' => $last7Days,
            'last30Days' => $last30Days,
            'topPaths' => $topPaths,
            'dailyLast14Days' => $dailyLast14Days,
        ];
    }
}
