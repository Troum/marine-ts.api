<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ApplicationFormSupplementaryDocumentServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationForm\ShowPublicSupplementaryDocumentRequest;
use App\Http\Requests\ApplicationForm\StorePublicSupplementaryDocumentRequest;
use Illuminate\Http\JsonResponse;

class ApplicationFormDocumentUploadController extends Controller
{
    public function __construct(
        private readonly ApplicationFormSupplementaryDocumentServiceInterface $supplementaryDocumentService,
    ) {}

    public function show(ShowPublicSupplementaryDocumentRequest $request, string $token): JsonResponse
    {
        $result = $this->supplementaryDocumentService->publicUploadSession($token);

        return response()->json($result['payload'], $result['status']);
    }

    public function store(StorePublicSupplementaryDocumentRequest $request, string $token): JsonResponse
    {
        $result = $this->supplementaryDocumentService->publicUploadStore($token, $request);

        return response()->json($result['payload'], $result['status']);
    }
}
