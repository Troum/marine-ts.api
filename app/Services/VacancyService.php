<?php

namespace App\Services;

use App\Contracts\Repositories\VacancyRepositoryInterface;
use App\Contracts\Services\VacancyServiceInterface;
use App\DTO\Vacancy\StoreVacancyDto;
use App\DTO\Vacancy\UpdateVacancyDto;
use App\Models\Vacancy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class VacancyService implements VacancyServiceInterface
{
    public function __construct(
        private readonly VacancyRepositoryInterface $vacancyRepository,
    ) {}

    public function paginatePublic(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->vacancyRepository->index($perPage, $page, ['published_only' => true]);
    }

    public function paginateAdmin(int $perPage, int $page, array $filters = []): LengthAwarePaginator
    {
        return $this->vacancyRepository->index($perPage, $page, array_merge([
            'with_application_forms_count' => true,
        ], $filters));
    }

    public function getById(int|string $id): Vacancy
    {
        /** @var Vacancy */
        return $this->vacancyRepository->getOne($id)->load('translations');
    }

    public function getBySlug(string $slug): Vacancy
    {
        return $this->vacancyRepository->findPublishedBySlugWithTranslations($slug);
    }

    public function create(StoreVacancyDto $dto): Vacancy
    {
        $default = (string) config('marine.default_locale');
        if (! isset($dto->translations[$default])) {
            throw new \InvalidArgumentException("translations.$default is required.");
        }

        $defaultRow = $dto->translations[$default];
        $slug = $dto->slug ?? $this->vacancyRepository->uniqueSlugForTitle(
            is_array($defaultRow) ? (string) ($defaultRow['title'] ?? '') : '',
        );

        return DB::transaction(function () use ($dto, $slug): Vacancy {
            /** @var Vacancy $vacancy */
            $vacancy = $this->vacancyRepository->createOne(array_filter([
                'slug' => $slug,
                'sort_order' => $dto->sort_order,
                'is_published' => $dto->is_published,
            ], static fn (mixed $v): bool => $v !== null));

            $this->vacancyRepository->syncVacancyTranslations($vacancy, $dto->translations);

            return $vacancy->load('translations');
        });
    }

    public function update(Vacancy $vacancy, UpdateVacancyDto $dto): Vacancy
    {
        $payload = $this->filterNulls([
            'slug' => $dto->slug,
            'sort_order' => $dto->sort_order,
            'is_published' => $dto->is_published,
        ]);
        if ($payload !== []) {
            $this->vacancyRepository->updateOne($vacancy, $payload);
        }

        if ($dto->translations !== null) {
            DB::transaction(function () use ($vacancy, $dto): void {
                $this->vacancyRepository->syncVacancyTranslations($vacancy, $dto->translations);
            });
        }

        return $vacancy->refresh()->load('translations');
    }

    public function delete(Vacancy $vacancy, bool $soft = true): void
    {
        $this->vacancyRepository->deleteOne($vacancy, $soft);
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
