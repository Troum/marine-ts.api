<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationForm\UpdateApplicationFormListsRequest;
use App\Services\ApplicationFormListsService;
use Illuminate\Http\JsonResponse;

final class ApplicationFormListsController extends Controller
{
    public function show(ApplicationFormListsService $service): JsonResponse
    {
        return response()->json($service->get());
    }

    public function update(UpdateApplicationFormListsRequest $request, ApplicationFormListsService $service): JsonResponse
    {
        $service->update(
            $request->validated('positionOptions'),
            $request->validated('vesselTypeOptions'),
        );

        return response()->json($service->get());
    }
}
