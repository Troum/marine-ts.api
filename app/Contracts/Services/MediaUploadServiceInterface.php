<?php

namespace App\Contracts\Services;

use Illuminate\Http\UploadedFile;

interface MediaUploadServiceInterface
{
    /**
     * Сохраняет файл в public disk и возвращает публичный URL.
     */
    public function storePublic(UploadedFile $file): string;
}
