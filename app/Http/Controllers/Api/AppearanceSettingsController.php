<?php

namespace App\Http\Controllers\Api;

use App\DTO\Appearance\UpdateAppearanceSettingsDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contacts\UpdateAppearanceSettingsRequest;
use App\Http\Resources\AppearanceSettingsResource;
use App\Services\AppearanceSettingsService;

class AppearanceSettingsController extends Controller
{
    public function __construct(
        private readonly AppearanceSettingsService $appearanceSettingsService,
    ) {}

    public function show(): AppearanceSettingsResource
    {
        return new AppearanceSettingsResource($this->appearanceSettingsService->getAppearance());
    }

    public function update(UpdateAppearanceSettingsRequest $request): AppearanceSettingsResource
    {
        $data = $this->appearanceSettingsService->updateAppearance(
            new UpdateAppearanceSettingsDto($request->validated()),
        );

        return new AppearanceSettingsResource($data);
    }
}
