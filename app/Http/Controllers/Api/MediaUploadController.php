<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\MediaUploadServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\StoreMediaUploadRequest;
use App\Http\Resources\MediaUploadedResource;
use Illuminate\Http\JsonResponse;

class MediaUploadController extends Controller
{
    public function __construct(
        private readonly MediaUploadServiceInterface $mediaUploadService,
    ) {}

    public function store(StoreMediaUploadRequest $request): JsonResponse
    {
        $url = $this->mediaUploadService->storePublic($request->file('file'));

        return (new MediaUploadedResource(['url' => $url]))
            ->response()
            ->setStatusCode(201);
    }
}
