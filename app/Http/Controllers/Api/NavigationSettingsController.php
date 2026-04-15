<?php

namespace App\Http\Controllers\Api;

use App\DTO\Navigation\UpdateNavigationSettingsDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNavigationSettingsRequest;
use App\Http\Resources\NavigationSettingsResource;
use App\Services\NavigationSettingsService;

class NavigationSettingsController extends Controller
{
    public function __construct(
        private readonly NavigationSettingsService $navigationSettingsService,
    ) {}

    public function show(): NavigationSettingsResource
    {
        return new NavigationSettingsResource($this->navigationSettingsService->getNavigation());
    }

    public function update(UpdateNavigationSettingsRequest $request): NavigationSettingsResource
    {
        $data = $this->navigationSettingsService->updateNavigation(new UpdateNavigationSettingsDto($request->validated()));

        return new NavigationSettingsResource($data);
    }
}
