<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ApplicationFormSupplementaryDocumentServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationForm\ShowPublicSupplementaryDocumentRequest;
use App\Http\Requests\ApplicationForm\StorePublicSupplementaryDocumentRequest;
use App\Http\Resources\ApiMessageResource;
use App\Http\Resources\PublicSupplementaryDocumentSessionResource;
use App\Http\Resources\PublicSupplementaryDocumentUploadResource;
use Illuminate\Http\JsonResponse;

class ApplicationFormDocumentUploadController extends Controller
{
    public function __construct(
        private readonly ApplicationFormSupplementaryDocumentServiceInterface $supplementaryDocumentService,
    ) {}

    public function show(ShowPublicSupplementaryDocumentRequest $request, string $token): JsonResponse
    {
        $result = $this->supplementaryDocumentService->publicUploadSession($token);

        if (($result['payload']['message'] ?? null) !== null) {
            return (new ApiMessageResource(['message' => (string) $result['payload']['message']]))
                ->response()
                ->setStatusCode($result['status']);
        }

        return (new PublicSupplementaryDocumentSessionResource($result['payload']['data']))
            ->response()
            ->setStatusCode($result['status']);
    }

    public function store(StorePublicSupplementaryDocumentRequest $request, string $token): JsonResponse
    {
        $result = $this->supplementaryDocumentService->publicUploadStore($token, $request);

        if (($result['payload']['message'] ?? null) !== null) {
            return (new ApiMessageResource(['message' => (string) $result['payload']['message']]))
                ->response()
                ->setStatusCode($result['status']);
        }

        return (new PublicSupplementaryDocumentUploadResource($result['payload']['data']))
            ->response()
            ->setStatusCode($result['status']);
    }
}
