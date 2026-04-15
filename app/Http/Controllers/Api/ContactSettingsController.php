<?php

namespace App\Http\Controllers\Api;

use App\DTO\Contact\UpdateContactSettingsDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateContactSettingsRequest;
use App\Http\Resources\ContactSettingsResource;
use App\Services\ContactSettingsService;

class ContactSettingsController extends Controller
{
    public function __construct(
        private readonly ContactSettingsService $contactSettingsService,
    ) {}

    public function show(): ContactSettingsResource
    {
        return new ContactSettingsResource($this->contactSettingsService->getContacts());
    }

    public function update(UpdateContactSettingsRequest $request): ContactSettingsResource
    {
        $data = $this->contactSettingsService->updateContacts(new UpdateContactSettingsDto($request->validated()));

        return new ContactSettingsResource($data);
    }
}
