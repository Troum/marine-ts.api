<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\SiteSeoServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\SiteSeo\UpdateSiteSeoPageRequest;
use App\Http\Resources\SiteSeoPageResource;
use App\Support\AdminListQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SiteSeoController extends Controller
{
    public function __construct(
        private readonly SiteSeoServiceInterface $siteSeoService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = array_merge(
            AdminListQuery::sortOrder($request, ['id', 'slug', 'label'], 'slug', 'asc'),
            array_filter(['search' => AdminListQuery::search($request)])
        );

        return SiteSeoPageResource::collection($this->siteSeoService->listForAdmin($filters));
    }

    public function show(string $slug): SiteSeoPageResource
    {
        return new SiteSeoPageResource($this->siteSeoService->getBySlug($slug));
    }

    public function update(UpdateSiteSeoPageRequest $request, string $slug): SiteSeoPageResource
    {
        return new SiteSeoPageResource($this->siteSeoService->updateSeo($slug, $request->validated()));
    }
}
