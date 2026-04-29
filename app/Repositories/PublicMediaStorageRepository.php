<?php

namespace App\Repositories;

use App\Contracts\Repositories\PublicMediaStorageRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class PublicMediaStorageRepository implements PublicMediaStorageRepositoryInterface
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    public function storePublicMedia(UploadedFile $file): string
    {
        $ext = $file->getClientOriginalExtension();
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = $name.'-'.Str::random(8).'.'.$ext;
        $path = $file->storeAs('media', $filename, 'public');

        return Storage::disk('public')->url($path);
    }

    public function listPublicMediaImages(): array
    {
        $disk = Storage::disk('public');

        if (! $disk->directoryExists('media')) {
            return [];
        }

        $items = [];
        foreach ($disk->files('media') as $path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (! in_array($ext, self::IMAGE_EXTENSIONS, true)) {
                continue;
            }
            $items[] = [
                'url' => $disk->url($path),
                'filename' => basename($path),
                'size' => $disk->size($path),
                'modified_at' => date(\DateTimeInterface::ATOM, $disk->lastModified($path)),
            ];
        }

        usort(
            $items,
            static fn (array $a, array $b): int => strcmp($b['modified_at'], $a['modified_at']),
        );

        return $items;
    }
}
