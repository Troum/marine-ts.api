<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/** Оптимизация JPEG/PNG и генерация WebP sidecar для каталога public/media. */
final class PublicMediaImageOptimizer
{
    public function optimize(string $absolutePath): void
    {
        if (! config('marine.media_image_optimize.enabled', true)) {
            return;
        }

        if (! is_file($absolutePath)) {
            return;
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        match ($ext) {
            'jpg', 'jpeg' => $this->optimizeJpeg($absolutePath),
            'png' => $this->optimizePng($absolutePath),
            default => null,
        };

        if (
            config('marine.media_image_optimize.generate_webp', true)
            && in_array($ext, ['jpg', 'jpeg', 'png'], true)
        ) {
            $this->generateWebpSidecar($absolutePath);
        }
    }

    private function optimizeJpeg(string $path): void
    {
        $binary = $this->findBinary('jpegoptim');
        if ($binary === null) {
            return;
        }

        $max = (int) config('marine.media_image_optimize.jpeg_max_quality', 85);
        $command = [$binary];
        if (config('marine.media_image_optimize.strip_exif', true)) {
            $command[] = '--strip-all';
        }
        $command[] = '--max='.$max;
        $command[] = $path;

        $this->run($command, $path);
    }

    private function optimizePng(string $path): void
    {
        $binary = $this->findBinary('optipng');
        if ($binary === null) {
            return;
        }

        $level = (int) config('marine.media_image_optimize.png_level', 2);
        $this->run([$binary, '-o'.$level, $path], $path);
    }

    private function generateWebpSidecar(string $path): void
    {
        $binary = $this->findBinary('cwebp');
        if ($binary === null) {
            return;
        }

        $quality = (int) config('marine.media_image_optimize.webp_quality', 82);
        $webpPath = preg_replace('/\.[^.]+$/', '.webp', $path);
        if (! is_string($webpPath) || $webpPath === $path) {
            return;
        }

        $this->run([$binary, '-q'.$quality, $path, '-o', $webpPath], $path);
    }

    /**
     * @param  list<string>  $command
     */
    private function run(array $command, string $path): void
    {
        $process = new Process($command);
        $process->setTimeout(120);
        $process->run();

        if (! $process->isSuccessful()) {
            Log::warning('Media image optimization failed.', [
                'path' => $path,
                'command' => $command,
                'exit_code' => $process->getExitCode(),
                'stderr' => trim($process->getErrorOutput()),
            ]);
        }
    }

    private function findBinary(string $name): ?string
    {
        static $cache = [];

        if (array_key_exists($name, $cache)) {
            return $cache[$name];
        }

        $binary = (new ExecutableFinder)->find($name);
        $cache[$name] = is_string($binary) && $binary !== '' ? $binary : null;

        if ($cache[$name] === null) {
            Log::debug('Media image optimizer binary not found, skipping.', ['binary' => $name]);
        }

        return $cache[$name];
    }
}
