<?php

namespace App\Services;

use App\Contracts\Repositories\ServiceRepositoryInterface;
use App\Contracts\Services\ServiceServiceInterface;
use App\DTO\Service\StoreServiceDto;
use App\DTO\Service\UpdateServiceDto;
use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
        return $this->serviceRepository->getOne($id);
    }

    public function create(StoreServiceDto $dto): Service
    {
        $data = [
            'title' => $dto->title,
            'description' => $dto->description,
            'features' => array_values($dto->features),
            'icon_key' => $dto->icon_key,
            'sort_order' => $dto->sort_order,
            'seo_title' => $dto->seo_title,
            'seo_description' => $dto->seo_description,
            'seo_keywords' => $dto->seo_keywords,
        ];

        return $this->serviceRepository->createOne($data);
    }

    public function update(Service $service, UpdateServiceDto $dto): Service
    {
        $payload = $this->filterNulls($dto->toArray());
        if (isset($payload['features']) && is_array($payload['features'])) {
            $payload['features'] = array_values($payload['features']);
        }
        if ($payload === []) {
            return $service->fresh() ?? $service;
        }
        $this->serviceRepository->updateOne($service, $payload);

        return $service->refresh();
    }

    public function delete(Service $service, bool $soft = true): void
    {
        $this->serviceRepository->deleteOne($service, $soft);
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
