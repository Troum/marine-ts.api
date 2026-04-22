<?php

namespace App\Http\Controllers\Api;

use App\DTO\Footer\UpdateFooterNavigationSettingsDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contacts\UpdateFooterNavigationSettingsRequest;
use App\Http\Resources\FooterNavigationSettingsResource;
use App\Services\FooterNavigationSettingsService;

class FooterNavigationSettingsController extends Controller
{
    public function __construct(
        private readonly FooterNavigationSettingsService $footerNavigationSettingsService,
    ) {}

    public function show(): FooterNavigationSettingsResource
    {
        return new FooterNavigationSettingsResource($this->footerNavigationSettingsService->getFooterNavigation());
    }

    public function update(UpdateFooterNavigationSettingsRequest $request): FooterNavigationSettingsResource
    {
        $data = $this->footerNavigationSettingsService->updateFooterNavigation(
            new UpdateFooterNavigationSettingsDto($request->validated()),
        );

        return new FooterNavigationSettingsResource($data);
    }
}
