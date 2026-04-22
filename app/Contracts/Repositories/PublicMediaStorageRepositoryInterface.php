<?php

namespace App\Contracts\Repositories;

use Illuminate\Http\UploadedFile;

interface PublicMediaStorageRepositoryInterface
{
    public function storePublicMedia(UploadedFile $file): string;
}
