<?php

namespace App\Repositories;

use App\Contracts\Repositories\PublicMediaStorageRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class PublicMediaStorageRepository implements PublicMediaStorageRepositoryInterface
{
    public function storePublicMedia(UploadedFile $file): string
    {
        $ext = $file->getClientOriginalExtension();
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = $name.'-'.Str::random(8).'.'.$ext;
        $path = $file->storeAs('media', $filename, 'public');

        return Storage::disk('public')->url($path);
    }
}
