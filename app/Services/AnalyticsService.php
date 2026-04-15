<?php

namespace App\Services;

use App\Contracts\Services\AnalyticsServiceInterface;
use App\DTO\Analytics\RecordPageViewDto;
use App\Models\PageView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class AnalyticsService implements AnalyticsServiceInterface
{
    public function recordPageView(RecordPageViewDto $dto, ?string $ip): void
    {
        $ipHash = hash('sha256', ($ip ?? '').config('app.key'));

        PageView::query()->create([
            'path' => Str::limit($dto->path, 2048, ''),
            'title' => $dto->title !== null && $dto->title !== '' ? Str::limit($dto->title, 512, '') : null,
            'referrer' => $dto->referrer !== null && $dto->referrer !== '' ? Str::limit($dto->referrer, 2048, '') : null,
            'ip_hash' => $ipHash,
            'created_at' => now(),
        ]);
    }

    public function getAdminSummary(): array
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
