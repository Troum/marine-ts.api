<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\VacancyServiceInterface;
use App\DTO\Vacancy\StoreVacancyDto;
use App\DTO\Vacancy\UpdateVacancyDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vacancy\DestroyVacancyRequest;
use App\Http\Requests\Vacancy\IndexPublicVacanciesRequest;
use App\Http\Requests\Vacancy\IndexVacanciesManageRequest;
use App\Http\Requests\Vacancy\StoreVacancyRequest;
use App\Http\Requests\Vacancy\UpdateVacancyRequest;
use App\Http\Resources\VacancyCollection;
use App\Http\Resources\VacancyResource;
use App\Models\Vacancy;
use Illuminate\Http\JsonResponse;
use Mycro\Core\Exceptions\DtoHydrationException;
use Mycro\Core\Exceptions\ReadonlyPropertyUpdateException;

class VacancyController extends Controller
{
    public function __construct(
        private readonly VacancyServiceInterface $vacancyService,
    ) {}

    public function index(IndexPublicVacanciesRequest $request): VacancyCollection
    {
        $dto = $request->toPaginatedTableDto();

        return new VacancyCollection($this->vacancyService->paginatePublic($dto->perPage, $dto->page));
    }

    public function manageIndex(IndexVacanciesManageRequest $request): VacancyCollection
    {
        $dto = $request->toPaginatedTableDto();

        return new VacancyCollection($this->vacancyService->paginateAdmin($dto->perPage, $dto->page, $dto->filters));
    }

    public function show(Vacancy $vacancy): VacancyResource
    {
        return new VacancyResource($vacancy->load('translations'));
    }

    public function showBySlug(string $slug): VacancyResource
    {
        $vacancy = $this->vacancyService->getBySlug($slug);

        return new VacancyResource($vacancy);
    }

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function store(StoreVacancyRequest $request)
    {
        $dto = new StoreVacancyDto($request->validated());
        $vacancy = $this->vacancyService->create($dto);

        return new VacancyResource($vacancy)->response()->setStatusCode(201);
    }

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function update(UpdateVacancyRequest $request, Vacancy $vacancy): VacancyResource
    {
        $dto = new UpdateVacancyDto($request->validated());
        $vacancy = $this->vacancyService->update($vacancy, $dto);

        return new VacancyResource($vacancy);
    }

    public function destroy(DestroyVacancyRequest $request, Vacancy $vacancy): JsonResponse
    {
        $this->vacancyService->delete($vacancy, true);

        return response()->json(null, 204);
    }
}
