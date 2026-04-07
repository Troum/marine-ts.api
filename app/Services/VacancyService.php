<?php

namespace App\Services;

use App\Contracts\Repositories\VacancyRepositoryInterface;
use App\Contracts\Services\VacancyServiceInterface;
use App\DTO\Vacancy\StoreVacancyDto;
use App\DTO\Vacancy\UpdateVacancyDto;
use App\Models\Vacancy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
        return $this->vacancyRepository->getOne($id);
    }

    public function getBySlug(string $slug): Vacancy
    {
        /** @var Vacancy */
        return Vacancy::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
    }

    public function create(StoreVacancyDto $dto): Vacancy
    {
        $data = [
            'title' => $dto->title,
            'slug' => $dto->slug,
            'excerpt' => $dto->excerpt,
            'content' => $dto->content,
            'requirements' => $dto->requirements !== null ? array_values($dto->requirements) : null,
            'location' => $dto->location,
            'employment_type' => $dto->employment_type,
            'sort_order' => $dto->sort_order,
            'is_published' => $dto->is_published,
            'seo_title' => $dto->seo_title,
            'seo_description' => $dto->seo_description,
            'seo_keywords' => $dto->seo_keywords,
        ];

        return $this->vacancyRepository->createOne(array_filter(
            $data,
            static fn (mixed $v): bool => $v !== null
        ));
    }

    public function update(Vacancy $vacancy, UpdateVacancyDto $dto): Vacancy
    {
        $payload = $this->filterNulls($dto->toArray());
        if (isset($payload['requirements']) && is_array($payload['requirements'])) {
            $payload['requirements'] = array_values($payload['requirements']);
        }
        if ($payload === []) {
            return $vacancy->fresh() ?? $vacancy;
        }
        $this->vacancyRepository->updateOne($vacancy, $payload);

        return $vacancy->refresh();
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
