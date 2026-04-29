<?php

namespace App\Contracts\Repositories;

use Illuminate\Http\UploadedFile;

interface PublicMediaStorageRepositoryInterface
{
    public function storePublicMedia(UploadedFile $file): string;

    /**
     * Список файлов изображений в каталоге public disk `media/` (не рекурсивно).
     *
     * @return list<array{url: string, filename: string, size: int, modified_at: string}>
     */
    public function listPublicMediaImages(): array;
}
