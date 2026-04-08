<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\DTO\Project\StoreProjectDto;
use App\DTO\Project\UpdateProjectDto;
use App\Models\Project;
use App\Support\MarineLocale;
use App\Support\NormalizeTranslationInput;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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
        return $this->projectRepository->getOne($id)->load(['translations', 'contentPage.translations']);
    }

    public function create(StoreProjectDto $dto): Project
    {
        $default = (string) config('marine.default_locale');
        if (! isset($dto->translations[$default])) {
            throw new \InvalidArgumentException("translations.$default is required.");
        }

        return DB::transaction(function () use ($dto): Project {
            /** @var Project $project */
            $project = $this->projectRepository->createOne(array_filter([
                'type' => $dto->type,
                'date' => $dto->date,
                'image' => $dto->image,
            ], static fn (mixed $v): bool => $v !== null));

            $this->syncProjectTranslations($project, $dto->translations);

            return $project->load(['translations', 'contentPage.translations']);
        });
    }

    public function update(Project $project, UpdateProjectDto $dto): Project
    {
        $payload = $this->filterNulls([
            'type' => $dto->type,
            'date' => $dto->date,
            'image' => $dto->image,
        ]);
        if ($payload !== []) {
            $this->projectRepository->updateOne($project, $payload);
        }

        if ($dto->translations !== null) {
            DB::transaction(function () use ($project, $dto): void {
                $this->syncProjectTranslations($project, $dto->translations);
            });
        }

        return $project->refresh()->load(['translations', 'contentPage.translations']);
    }

    public function delete(Project $project, bool $soft = true): void
    {
        $this->projectRepository->deleteOne($project, $soft);
    }

    /**
     * @param  array<string, array<string, mixed>>  $translations
     */
    private function syncProjectTranslations(Project $project, array $translations): void
    {
        foreach (config('marine.locales') as $locale) {
            if (! isset($translations[$locale])) {
                continue;
            }
            if (! MarineLocale::isSupported((string) $locale)) {
                continue;
            }
            $row = NormalizeTranslationInput::projectLocaleRow($translations[$locale]);
            $project->translations()->updateOrCreate(
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
