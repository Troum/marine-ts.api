<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\GalleryItemServiceInterface;
use App\DTO\GalleryItem\StoreGalleryItemDto;
use App\DTO\GalleryItem\UpdateGalleryItemDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\GalleryItem\DestroyGalleryItemRequest;
use App\Http\Requests\GalleryItem\ManageGalleryIndexRequest;
use App\Http\Requests\GalleryItem\PublicGalleryIndexRequest;
use App\Http\Requests\GalleryItem\ReplaceGalleryImageRequest;
use App\Http\Requests\GalleryItem\StoreGalleryItemRequest;
use App\Http\Requests\GalleryItem\UpdateGalleryItemRequest;
use App\Http\Resources\GalleryItemResource;
use App\Models\GalleryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mycro\Core\Exceptions\DtoHydrationException;
use Mycro\Core\Exceptions\ReadonlyPropertyUpdateException;

class GalleryItemController extends Controller
{
    public function __construct(
        private readonly GalleryItemServiceInterface $galleryItemService,
    ) {}

    public function index(PublicGalleryIndexRequest $request): AnonymousResourceCollection
    {
        return GalleryItemResource::collection($this->galleryItemService->listForManage());
    }

    /** Список для админки: те же данные, путь `/gallery/manage` включает полные `translations` в JSON. */
    public function manageIndex(ManageGalleryIndexRequest $request): AnonymousResourceCollection
    {
        return GalleryItemResource::collection($this->galleryItemService->listForManage());
    }

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function store(StoreGalleryItemRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $image = $request->file('image');
        unset($validated['image']);

        $dto = new StoreGalleryItemDto($validated);
        $item = $this->galleryItemService->create($dto, $image);

        return new GalleryItemResource($item)->response()->setStatusCode(201);
    }

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function update(UpdateGalleryItemRequest $request, GalleryItem $gallery_item): GalleryItemResource
    {
        $validated = $request->validated();
        $dto = new UpdateGalleryItemDto($validated);
        $item = $this->galleryItemService->update($gallery_item, $dto, array_keys($validated));

        return new GalleryItemResource($item);
    }

    public function replaceImage(ReplaceGalleryImageRequest $request, GalleryItem $gallery_item): GalleryItemResource
    {
        $item = $this->galleryItemService->replaceImage($gallery_item, $request->file('image'));

        return new GalleryItemResource($item);
    }

    public function destroy(DestroyGalleryItemRequest $request, GalleryItem $gallery_item): JsonResponse
    {
        $this->galleryItemService->delete($gallery_item);

        return response()->json(null, 204);
    }
}
