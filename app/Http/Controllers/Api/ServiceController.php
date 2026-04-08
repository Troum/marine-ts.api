<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ServiceServiceInterface;
use App\DTO\Service\StoreServiceDto;
use App\DTO\Service\UpdateServiceDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Service\DestroyServiceRequest;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\ServiceCollection;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Support\AdminListQuery;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceServiceInterface $serviceService,
    ) {}

    public function index(Request $request): ServiceCollection
    {
        $perPage = min(max((int) $request->query('per_page', 100), 1), 500);
        $page = max(1, (int) $request->query('page', 1));

        $filters = array_merge(
            AdminListQuery::sortOrder($request, ['id', 'title', 'sort_order', 'icon_key'], 'sort_order', 'asc'),
            array_filter(['search' => AdminListQuery::search($request)])
        );

        return new ServiceCollection($this->serviceService->paginate($perPage, $page, $filters));
    }

    public function show(Service $service): ServiceResource
    {
        return new ServiceResource($service->load(['translations', 'contentPage.translations']));
    }

    public function store(StoreServiceRequest $request)
    {
        $dto = new StoreServiceDto($request->validated());
        $service = $this->serviceService->create($dto);

        return (new ServiceResource($service->load(['translations', 'contentPage.translations'])))->response()->setStatusCode(201);
    }

    public function update(UpdateServiceRequest $request, Service $service): ServiceResource
    {
        $dto = new UpdateServiceDto($request->validated());
        $service = $this->serviceService->update($service, $dto);

        return new ServiceResource($service->load(['translations', 'contentPage.translations']));
    }

    public function destroy(DestroyServiceRequest $request, Service $service)
    {
        $this->serviceService->delete($service, true);

        return response()->json(null, 204);
    }
}
