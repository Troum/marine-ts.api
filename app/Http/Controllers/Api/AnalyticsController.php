<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\AnalyticsServiceInterface;
use App\DTO\Analytics\RecordPageViewDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Analytics\StorePageViewRequest;
use App\Http\Resources\AnalyticsAdminSummaryResource;
use Illuminate\Http\Response;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsServiceInterface $analyticsService,
    ) {}

    public function storePageView(StorePageViewRequest $request): Response
    {
        $this->analyticsService->recordPageView(
            new RecordPageViewDto($request->validated()),
            $request->ip(),
        );

        return response()->noContent();
    }

    public function manageSummary(): AnalyticsAdminSummaryResource
    {
        return new AnalyticsAdminSummaryResource($this->analyticsService->getAdminSummary());
    }
}
