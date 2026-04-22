<?php

namespace App\Services;

use App\Contracts\Repositories\PublicMediaStorageRepositoryInterface;
use App\Contracts\Services\MediaUploadServiceInterface;
use Illuminate\Http\UploadedFile;

final class MediaUploadService implements MediaUploadServiceInterface
{
    public function __construct(
        private readonly PublicMediaStorageRepositoryInterface $publicMediaStorageRepository,
    ) {}

    public function storePublic(UploadedFile $file): string
    {
        return $this->publicMediaStorageRepository->storePublicMedia($file);
    }
}
