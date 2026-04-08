<?php

namespace App\Services;

use App\Contracts\Repositories\ServiceRepositoryInterface;
use App\Contracts\Services\ServiceServiceInterface;
use App\DTO\Service\StoreServiceDto;
use App\DTO\Service\UpdateServiceDto;
use App\Models\Service;
use App\Support\MarineLocale;
use App\Support\NormalizeTranslationInput;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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

    public function create(StoreServiceDto $dto): Service
    {
        $default = (string) config('marine.default_locale');
        if (! isset($dto->translations[$default])) {
            throw new \InvalidArgumentException("translations.$default is required.");
        }

        return DB::transaction(function () use ($dto): Service {
            /** @var Service $service */
            $service = $this->serviceRepository->createOne([
                'icon_key' => $dto->icon_key,
                'sort_order' => $dto->sort_order,
            ]);

            $this->syncServiceTranslations($service, $dto->translations);

            return $service->load(['translations', 'contentPage.translations']);
        });
    }

    public function update(Service $service, UpdateServiceDto $dto): Service
    {
        $payload = $this->filterNulls([
            'icon_key' => $dto->icon_key,
            'sort_order' => $dto->sort_order,
        ]);
        if ($payload !== []) {
            $this->serviceRepository->updateOne($service, $payload);
        }

        if ($dto->translations !== null) {
            DB::transaction(function () use ($service, $dto): void {
                $this->syncServiceTranslations($service, $dto->translations);
            });
        }

        return $service->refresh()->load(['translations', 'contentPage.translations']);
    }

    public function delete(Service $service, bool $soft = true): void
    {
        $this->serviceRepository->deleteOne($service, $soft);
    }

    /**
     * @param  array<string, array<string, mixed>>  $translations
     */
    private function syncServiceTranslations(Service $service, array $translations): void
    {
        foreach (config('marine.locales') as $locale) {
            if (! isset($translations[$locale])) {
                continue;
            }
            if (! MarineLocale::isSupported((string) $locale)) {
                continue;
            }
            $row = NormalizeTranslationInput::serviceLocaleRow($translations[$locale]);
            $service->translations()->updateOrCreate(
                ['locale' => $locale],
                $row
            );
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
