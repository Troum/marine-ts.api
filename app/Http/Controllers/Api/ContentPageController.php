<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ContentPageServiceInterface;
use App\DTO\ContentPage\StoreContentPageDto;
use App\DTO\ContentPage\UpdateContentPageDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContentPage\DestroyContentPageRequest;
use App\Http\Requests\ContentPage\IndexContentPagesRequest;
use App\Http\Requests\ContentPage\ShowContentPageManageRequest;
use App\Http\Requests\ContentPage\StoreContentPageRequest;
use App\Http\Requests\ContentPage\UpdateContentPageRequest;
use App\Http\Resources\ContentPageCollection;
use App\Http\Resources\ContentPageResource;
use App\Http\Resources\ContentPageSummaryResource;
use App\Models\ContentPage;
use App\Support\AdminListQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InvalidArgumentException;
use Mycro\Core\Exceptions\DtoHydrationException;
use Mycro\Core\Exceptions\ReadonlyPropertyUpdateException;

class ContentPageController extends Controller
{
    public function __construct(
        private readonly ContentPageServiceInterface $contentPageService,
    ) {}

    public function publicIndex(Request $request): AnonymousResourceCollection
    {
        $pages = $this->contentPageService->listPublishedForPublic();

        return ContentPageSummaryResource::collection($pages);
    }

    public function publicShow(string $slug): ContentPageResource|JsonResponse
    {
        $page = $this->contentPageService->findPublishedBySlug($slug);
        if ($page === null) {
            return response()->json(['message' => 'Страница не найдена'], 404);
        }

        return new ContentPageResource($page->load(['contentable.translations', 'translations']));
    }

    public function manageIndex(IndexContentPagesRequest $request): ContentPageCollection
    {
        $perPage = min(max((int) $request->query('per_page', 100), 1), 500);
        $page = max(1, (int) $request->query('page', 1));

        $published = AdminListQuery::publishedTriState($request);
        $filters = array_merge(
            AdminListQuery::sortOrder($request, ['id', 'slug', 'title', 'sort_order', 'created_at', 'updated_at'], 'sort_order', 'asc'),
            array_filter(['search' => AdminListQuery::search($request)])
        );
        if ($published !== null) {
            $filters['published_filter'] = $published;
        }

        return new ContentPageCollection($this->contentPageService->paginateManage($perPage, $page, $filters));
    }

    public function showManage(ShowContentPageManageRequest $request, ContentPage $contentPage): ContentPageResource
    {
        return new ContentPageResource($contentPage->load(['contentable.translations', 'translations']));
    }

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function store(StoreContentPageRequest $request)
    {
        $validated = $request->validated();
        $link = $this->extractContentableForStore($validated);
        $dto = new StoreContentPageDto($this->normalizeValidatedForDto($this->stripContentableKeys($validated)));
        $page = $this->contentPageService->create($dto, $link[0], $link[1]);

        return (new ContentPageResource($page->load(['contentable.translations', 'translations'])))->response()->setStatusCode(201);
    }

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function update(UpdateContentPageRequest $request, ContentPage $contentPage): ContentPageResource
    {
        $validated = $request->validated();
        $sync = $this->extractContentableForUpdate($validated);
        $dto = new UpdateContentPageDto($this->normalizeValidatedForDto($this->stripContentableKeys($validated)));
        $page = $this->contentPageService->update(
            $contentPage,
            $dto,
            $sync['sync'],
            $sync['type'],
            $sync['id'],
        );

        return new ContentPageResource($page->load(['contentable.translations', 'translations']));
    }

    public function destroy(DestroyContentPageRequest $request, ContentPage $contentPage)
    {
        $this->contentPageService->delete($contentPage);

        return response()->json(null, 204);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function normalizeValidatedForDto(array $validated): array
    {
        if (array_key_exists('isPublished', $validated)) {
            $validated['is_published'] = $validated['isPublished'];
            unset($validated['isPublished']);
        }
        if (array_key_exists('sortOrder', $validated)) {
            $validated['sort_order'] = $validated['sortOrder'];
            unset($validated['sortOrder']);
        }
        if (array_key_exists('seoTitle', $validated)) {
            $validated['seo_title'] = $validated['seoTitle'];
            unset($validated['seoTitle']);
        }
        if (array_key_exists('seoDescription', $validated)) {
            $validated['seo_description'] = $validated['seoDescription'];
            unset($validated['seoDescription']);
        }
        if (array_key_exists('seoKeywords', $validated)) {
            $validated['seo_keywords'] = $validated['seoKeywords'];
            unset($validated['seoKeywords']);
        }

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{0: ?string, 1: ?int}
     */
    private function extractContentableForStore(array $validated): array
    {
        if (! $this->validatedHasAnyContentableKey($validated)) {
            return [null, null];
        }
        $type = $validated['contentableType'] ?? $validated['contentable_type'] ?? null;
        $id = $validated['contentableId'] ?? $validated['contentable_id'] ?? null;
        if ($type === null && $id === null) {
            return [null, null];
        }
        if ($type === null || $id === null) {
            throw new InvalidArgumentException('contentableType and contentableId must be set together.');
        }

        return [(string) $type, (int) $id];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{sync: bool, type: ?string, id: ?int}
     */
    private function extractContentableForUpdate(array $validated): array
    {
        if (! $this->validatedHasAnyContentableKey($validated)) {
            return ['sync' => false, 'type' => null, 'id' => null];
        }
        $type = $validated['contentableType'] ?? $validated['contentable_type'] ?? null;
        $id = $validated['contentableId'] ?? $validated['contentable_id'] ?? null;
        if ($type === null && $id === null) {
            return ['sync' => true, 'type' => null, 'id' => null];
        }
        if ($type === null || $id === null) {
            throw new InvalidArgumentException('contentableType and contentableId must be set together.');
        }

        return ['sync' => true, 'type' => (string) $type, 'id' => (int) $id];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function validatedHasAnyContentableKey(array $validated): bool
    {
        return array_key_exists('contentableType', $validated)
            || array_key_exists('contentableId', $validated)
            || array_key_exists('contentable_type', $validated)
            || array_key_exists('contentable_id', $validated);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function stripContentableKeys(array $validated): array
    {
        unset(
            $validated['contentableType'],
            $validated['contentableId'],
            $validated['contentable_type'],
            $validated['contentable_id'],
        );

        return $validated;
    }
}
