<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ApplicationFormSupplementaryDocumentServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationFormDocumentUploadController extends Controller
{
    public function __construct(
        private readonly ApplicationFormSupplementaryDocumentServiceInterface $supplementaryDocumentService,
    ) {}

    public function show(string $token): JsonResponse
    {
        $result = $this->supplementaryDocumentService->publicUploadSession($token);

        return response()->json($result['payload'], $result['status']);
    }

    public function store(Request $request, string $token): JsonResponse
    {
        $result = $this->supplementaryDocumentService->publicUploadStore($token, $request);

        return response()->json($result['payload'], $result['status']);
    }
}
