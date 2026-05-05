<?php

namespace App\Contracts\Services;

use Illuminate\Http\UploadedFile;

interface MediaUploadServiceInterface
{
    /**
     * Сохраняет файл в public disk и возвращает публичный URL.
     */
    public function storePublic(UploadedFile $file): string;

    /**
     * @return list<array{url: string, filename: string, size: int, modified_at: string}>
     *         Изображения и видео из public/storage/media.
     */
    public function listPublicImages(): array;
}
