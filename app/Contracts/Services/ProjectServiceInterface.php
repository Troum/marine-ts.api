<?php

namespace App\Contracts\Services;

use App\DTO\Project\StoreProjectDto;
use App\DTO\Project\UpdateProjectDto;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProjectServiceInterface
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(int $perPage, int $page, array $filters = []): LengthAwarePaginator;

    public function getById(int|string $id): Project;

    public function create(StoreProjectDto $dto): Project;

    public function update(Project $project, UpdateProjectDto $dto): Project;

    public function delete(Project $project, bool $soft = true): void;
}
