<?php

namespace App\Services;

use App\Contracts\Services\MediaUploadServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class MediaUploadService implements MediaUploadServiceInterface
{
    public function storePublic(UploadedFile $file): string
    {
        $ext = $file->getClientOriginalExtension();
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = $name.'-'.Str::random(8).'.'.$ext;
        $path = $file->storeAs('media', $filename, 'public');

        return Storage::disk('public')->url($path);
    }
}
