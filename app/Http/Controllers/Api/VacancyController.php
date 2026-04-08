<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\VacancyServiceInterface;
use App\DTO\Vacancy\StoreVacancyDto;
use App\DTO\Vacancy\UpdateVacancyDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vacancy\DestroyVacancyRequest;
use App\Http\Requests\Vacancy\StoreVacancyRequest;
use App\Http\Requests\Vacancy\UpdateVacancyRequest;
use App\Http\Resources\VacancyCollection;
use App\Http\Resources\VacancyResource;
use App\Models\Vacancy;
use App\Support\AdminListQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mycro\Core\Exceptions\DtoHydrationException;
use Mycro\Core\Exceptions\ReadonlyPropertyUpdateException;

class VacancyController extends Controller
{
    public function __construct(
        private readonly VacancyServiceInterface $vacancyService,
    ) {}

    public function index(Request $request): VacancyCollection
    {
        $perPage = min(max((int) $request->query('per_page', 100), 1), 500);
        $page = max(1, (int) $request->query('page', 1));

        return new VacancyCollection($this->vacancyService->paginatePublic($perPage, $page));
    }

    public function manageIndex(Request $request): VacancyCollection
    {
        $perPage = min(max((int) $request->query('per_page', 100), 1), 500);
        $page = max(1, (int) $request->query('page', 1));

        $published = AdminListQuery::publishedTriState($request);
        $filters = array_merge(
            AdminListQuery::sortOrder($request, ['id', 'title', 'slug', 'sort_order', 'is_published', 'location', 'employment_type', 'created_at', 'updated_at'], 'sort_order', 'asc'),
            array_filter(['search' => AdminListQuery::search($request)])
        );
        if ($published !== null) {
            $filters['published_filter'] = $published;
        }

        return new VacancyCollection($this->vacancyService->paginateAdmin($perPage, $page, $filters));
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
