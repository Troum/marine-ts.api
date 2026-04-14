<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNavigationSettingsRequest;
use App\Services\NavigationSettingsService;
use Illuminate\Http\JsonResponse;

class NavigationSettingsController extends Controller
{
    public function __construct(
        private readonly NavigationSettingsService $navigationSettingsService,
    ) {}

    public function show(): JsonResponse
    {
        return response()->json([
            'data' => $this->navigationSettingsService->getNavigation(),
        ]);
    }

    public function update(UpdateNavigationSettingsRequest $request): JsonResponse
    {
        $data = $this->navigationSettingsService->updateNavigation($request->validated());

        return response()->json([
            'data' => $data,
        ]);
    }
}
