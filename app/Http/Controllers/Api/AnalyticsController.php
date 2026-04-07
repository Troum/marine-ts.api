<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\AnalyticsServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Analytics\StorePageViewRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsServiceInterface $analyticsService,
    ) {}

    public function storePageView(StorePageViewRequest $request): Response
    {
        $validated = $request->validated();

        $this->analyticsService->recordPageView(
            $validated['path'],
            $validated['title'] ?? null,
            $validated['referrer'] ?? null,
            $request->ip(),
        );

        return response()->noContent();
    }

    public function manageSummary(): JsonResponse
    {
        return response()->json($this->analyticsService->getAdminSummary());
    }
}
