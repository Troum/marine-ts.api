<?php

namespace App\Services;

use App\Contracts\Repositories\PageViewRepositoryInterface;
use App\Contracts\Services\AnalyticsServiceInterface;
use App\DTO\Analytics\RecordPageViewDto;
use Illuminate\Support\Str;

final class AnalyticsService implements AnalyticsServiceInterface
{
    public function __construct(
        private readonly PageViewRepositoryInterface $pageViewRepository,
    ) {}

    public function recordPageView(RecordPageViewDto $dto, ?string $ip): void
    {
        $ipHash = hash('sha256', ($ip ?? '').config('app.key'));

        $this->pageViewRepository->createOne([
            'path' => Str::limit($dto->path, 2048, ''),
            'title' => $dto->title !== null && $dto->title !== '' ? Str::limit($dto->title, 512, '') : null,
            'referrer' => $dto->referrer !== null && $dto->referrer !== '' ? Str::limit($dto->referrer, 2048, '') : null,
            'ip_hash' => $ipHash,
            'created_at' => now(),
        ]);
    }

    public function getAdminSummary(): array
    {
        return $this->pageViewRepository->adminSummary();
    }
}
