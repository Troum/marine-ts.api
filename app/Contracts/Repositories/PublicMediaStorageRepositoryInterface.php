<?php

namespace App\Contracts\Repositories;

use Illuminate\Http\UploadedFile;

interface PublicMediaStorageRepositoryInterface
{
    public function storePublicMedia(UploadedFile $file): string;

    /**
     * Список файлов в каталоге public disk `media/` (не рекурсивно): изображения и видео (mp4, webm, mov).
     *
     * @return list<array{url: string, filename: string, size: int, modified_at: string}>
     */
    public function listPublicMediaImages(): array;
}
