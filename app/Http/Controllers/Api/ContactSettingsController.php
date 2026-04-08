<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateContactSettingsRequest;
use App\Services\ContactSettingsService;
use Illuminate\Http\JsonResponse;

class ContactSettingsController extends Controller
{
    public function __construct(
        private readonly ContactSettingsService $contactSettingsService,
    ) {}

    public function show(): JsonResponse
    {
        return response()->json([
            'data' => $this->contactSettingsService->getContacts(),
        ]);
    }

    public function update(UpdateContactSettingsRequest $request): JsonResponse
    {
        $data = $this->contactSettingsService->updateContacts($request->validated());

        return response()->json([
            'data' => $data,
        ]);
    }
}
