<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\DTO\Project\StoreProjectDto;
use App\DTO\Project\UpdateProjectDto;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ProjectService implements ProjectServiceInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
    ) {}

    public function paginate(int $perPage, int $page, array $filters = []): LengthAwarePaginator
    {
        return $this->projectRepository->index($perPage, $page, $filters);
    }

    public function getById(int|string $id): Project
    {
        /** @var Project */
        return $this->projectRepository->getOne($id);
    }

    public function create(StoreProjectDto $dto): Project
    {
        $data = [
            'title' => $dto->title,
            'type' => $dto->type,
            'type_label' => $dto->type_label,
            'location' => $dto->location,
            'date' => $dto->date,
            'description' => $dto->description,
            'stats' => $dto->stats,
            'image' => $dto->image,
            'seo_title' => $dto->seo_title,
            'seo_description' => $dto->seo_description,
            'seo_keywords' => $dto->seo_keywords,
        ];

        return $this->projectRepository->createOne(array_filter(
            $data,
            static fn (mixed $v): bool => $v !== null
        ));
    }

    public function update(Project $project, UpdateProjectDto $dto): Project
    {
        $payload = $this->filterNulls($dto->toArray());
        if ($payload === []) {
            return $project->fresh() ?? $project;
        }
        $this->projectRepository->updateOne($project, $payload);

        return $project->refresh();
    }

    public function delete(Project $project, bool $soft = true): void
    {
        $this->projectRepository->deleteOne($project, $soft);
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
