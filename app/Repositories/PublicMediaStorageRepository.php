<?php

namespace App\Repositories;

use App\Contracts\Repositories\PublicMediaStorageRepositoryInterface;
use App\Support\PublicMediaImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

final class PublicMediaStorageRepository implements PublicMediaStorageRepositoryInterface
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /** Видео для фонов hero и т.п. (тот же каталог `storage/media`). */
    private const VIDEO_EXTENSIONS = ['mp4', 'webm', 'mov'];

    /** Расширения, для которых рядом может лежать WebP sidecar (не показываем в медиатеке). */
    private const WEBP_SIDECAR_SOURCE_EXTENSIONS = ['jpg', 'jpeg', 'png'];

    public function __construct(
        private readonly PublicMediaImageOptimizer $publicMediaImageOptimizer,
    ) {}

    public function storePublicMedia(UploadedFile $file): string
    {
        $ext = strtolower((string) $file->getClientOriginalExtension());
        $name = Str::slug((string) pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        if ($name === '') {
            $name = 'media';
        }
        if ($ext === '') {
            $ext = strtolower((string) $file->extension());
        }
        if ($ext === '') {
            throw new RuntimeException('Unable to detect uploaded file extension.');
        }

        $filename = $name.'-'.Str::random(8).'.'.$ext;
        $disk = Storage::disk('public');

        if (! $disk->directoryExists('media') && ! $disk->makeDirectory('media')) {
            Log::error('Public media upload failed: unable to create media directory.', [
                'disk' => 'public',
                'filename' => $filename,
            ]);
            throw new RuntimeException('Failed to create media directory in public disk.');
        }

        $path = $disk->putFileAs('media', $file, $filename);

        if (! is_string($path) || $path === '') {
            Log::error('Public media upload failed: empty storage path returned.', [
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'tmp_path' => $file->getRealPath(),
                'target_dir' => 'media',
            ]);
            throw new RuntimeException('Failed to store uploaded file in public disk.');
        }

        if (in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            $this->publicMediaImageOptimizer->optimize($disk->path($path));
        }

        $url = $disk->url($path);
        if ($url === '' || Str::endsWith($url, '/storage')) {
            Log::error('Public media upload produced invalid URL.', [
                'path' => $path,
                'url' => $url,
                'filename' => $filename,
            ]);
            throw new RuntimeException('Failed to resolve public URL for uploaded file.');
        }

        return $url;
    }

    public function listPublicMediaImages(): array
    {
        $disk = Storage::disk('public');

        if (! $disk->directoryExists('media')) {
            return [];
        }

        $paths = $disk->files('media');
        $filenames = array_map(static fn (string $path): string => basename($path), $paths);

        $items = [];
        foreach ($paths as $path) {
            $filename = basename($path);
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (! in_array($ext, self::IMAGE_EXTENSIONS, true) && ! in_array($ext, self::VIDEO_EXTENSIONS, true)) {
                continue;
            }
            if ($this->isWebpSidecar($filename, $filenames)) {
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

    /**
     * @param  list<string>  $filenames
     */
    private function isWebpSidecar(string $filename, array $filenames): bool
    {
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== 'webp') {
            return false;
        }

        $stem = pathinfo($filename, PATHINFO_FILENAME);
        foreach (self::WEBP_SIDECAR_SOURCE_EXTENSIONS as $sourceExt) {
            if (in_array($stem.'.'.$sourceExt, $filenames, true)) {
                return true;
            }
        }

        return false;
    }
}
