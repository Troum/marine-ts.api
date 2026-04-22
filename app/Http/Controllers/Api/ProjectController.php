<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ProjectServiceInterface;
use App\DTO\Project\StoreProjectDto;
use App\DTO\Project\UpdateProjectDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\DestroyProjectRequest;
use App\Http\Requests\Project\IndexProjectRequest;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Mycro\Core\Exceptions\DtoHydrationException;
use Mycro\Core\Exceptions\ReadonlyPropertyUpdateException;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectServiceInterface $projectService,
    ) {}

    public function index(IndexProjectRequest $request): ProjectCollection
    {
        $dto = $request->toPaginatedTableDto();

        return new ProjectCollection($this->projectService->paginate($dto->perPage, $dto->page, $dto->filters));
    }

    public function show(Project $project): ProjectResource
    {
        return new ProjectResource($project->load(['translations', 'contentPage.translations']));
    }

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $dto = new StoreProjectDto($request->validated());
        $project = $this->projectService->create($dto);

        return new ProjectResource($project->load(['translations', 'contentPage.translations']))->response()->setStatusCode(201);
    }

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $dto = new UpdateProjectDto($request->validated());
        $project = $this->projectService->update($project, $dto);

        return new ProjectResource($project->load(['translations', 'contentPage.translations']));
    }

    public function destroy(DestroyProjectRequest $request, Project $project): JsonResponse
    {
        $this->projectService->delete($project, true);

        return response()->json(null, 204);
    }
}
