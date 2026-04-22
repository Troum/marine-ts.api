<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\SiteSeoServiceInterface;
use App\DTO\SiteSeo\UpdateSiteSeoPageDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\SiteSeo\IndexSiteSeoPagesRequest;
use App\Http\Requests\SiteSeo\UpdateSiteSeoPageRequest;
use App\Http\Resources\SiteSeoPageResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SiteSeoController extends Controller
{
    public function __construct(
        private readonly SiteSeoServiceInterface $siteSeoService,
    ) {}

    public function index(IndexSiteSeoPagesRequest $request): AnonymousResourceCollection
    {
        $dto = $request->toPaginatedTableDto();

        return SiteSeoPageResource::collection($this->siteSeoService->listForAdmin($dto->filters));
    }

    public function show(string $slug): SiteSeoPageResource
    {
        return new SiteSeoPageResource($this->siteSeoService->getBySlug($slug));
    }

    public function update(UpdateSiteSeoPageRequest $request, string $slug): SiteSeoPageResource
    {
        return new SiteSeoPageResource($this->siteSeoService->updateSeo($slug, new UpdateSiteSeoPageDto($request->validated())));
    }
}
