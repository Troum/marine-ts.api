<?php

namespace App\Contracts\Services;

use App\DTO\Vacancy\StoreVacancyDto;
use App\DTO\Vacancy\UpdateVacancyDto;
use App\Models\Vacancy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VacancyServiceInterface
{
    public function paginatePublic(int $perPage, int $page): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateAdmin(int $perPage, int $page, array $filters = []): LengthAwarePaginator;

    public function getById(int|string $id): Vacancy;

    public function getBySlug(string $slug): Vacancy;

    public function create(StoreVacancyDto $dto): Vacancy;

    public function update(Vacancy $vacancy, UpdateVacancyDto $dto): Vacancy;

    public function delete(Vacancy $vacancy, bool $soft = true): void;
}
