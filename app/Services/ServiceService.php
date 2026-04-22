<?php

namespace App\Services;

use App\Contracts\Repositories\ServiceRepositoryInterface;
use App\Contracts\Services\ServiceServiceInterface;
use App\DTO\Service\StoreServiceDto;
use App\DTO\Service\UpdateServiceDto;
use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class ServiceService implements ServiceServiceInterface
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository,
    ) {}

    public function paginate(int $perPage, int $page, array $filters = []): LengthAwarePaginator
    {
        return $this->serviceRepository->index($perPage, $page, $filters);
    }

    public function getById(int|string $id): Service
    {
        /** @var Service */
        return $this->serviceRepository->getOne($id)->load(['translations', 'contentPage.translations']);
    }

    public function create(StoreServiceDto $dto, ?UploadedFile $image = null): Service
    {
        $default = (string) config('marine.default_locale');
        if (! isset($dto->translations[$default])) {
            throw new \InvalidArgumentException("translations.$default is required.");
        }

        return DB::transaction(function () use ($dto, $image): Service {
            $imagePath = $image !== null ? $this->storeServiceImage($image) : null;

            /** @var Service $service */
            $service = $this->serviceRepository->createOne([
                'icon_key' => $dto->icon_key,
                'sort_order' => $dto->sort_order,
                'image_path' => $imagePath,
            ]);

            $this->serviceRepository->syncServiceTranslations($service, $dto->translations);

            return $service->load(['translations', 'contentPage.translations']);
        });
    }

    public function update(Service $service, UpdateServiceDto $dto, ?UploadedFile $image = null): Service
    {
        $payload = $this->filterNulls([
            'icon_key' => $dto->icon_key,
            'sort_order' => $dto->sort_order,
        ]);

        $clearImage = (bool) ($dto->clear_image ?? false);

        if ($image !== null) {
            $newPath = $this->storeServiceImage($image);
            $payload['image_path'] = $newPath;
            $oldPath = $service->image_path;
        } elseif ($clearImage) {
            $payload['image_path'] = null;
            $oldPath = $service->image_path;
        } else {
            $oldPath = null;
        }

        if ($payload !== []) {
            $this->serviceRepository->updateOne($service, $payload);
        }

        if ($image !== null || $clearImage) {
            $this->deleteStoredFileIfManaged($oldPath ?? null);
        }

        if ($dto->translations !== null) {
            DB::transaction(function () use ($service, $dto): void {
                $this->serviceRepository->syncServiceTranslations($service, $dto->translations);
            });
        }

        return $service->refresh()->load(['translations', 'contentPage.translations']);
    }

    public function delete(Service $service, bool $soft = true): void
    {
        $path = $service->image_path;
        $this->serviceRepository->deleteOne($service, $soft);
        $this->deleteStoredFileIfManaged($path);
    }

    private function storeServiceImage(UploadedFile $image): string
    {
        /** @var string $path */
        $path = $image->store('services', 'public');

        return $path;
    }

    private function deleteStoredFileIfManaged(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }
        if (! str_starts_with($path, 'services/')) {
            return;
        }
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function filterNulls(array $data): array
    {
        return array_filter($data, static fn (mixed $v): bool => $v !== null);
    }
}
