<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GalleryItem\DestroyGalleryItemRequest;
use App\Http\Requests\GalleryItem\ReplaceGalleryImageRequest;
use App\Http\Requests\GalleryItem\StoreGalleryItemRequest;
use App\Http\Requests\GalleryItem\UpdateGalleryItemRequest;
use App\Http\Resources\GalleryItemResource;
use App\Models\GalleryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class GalleryItemController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $items = GalleryItem::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return GalleryItemResource::collection($items);
    }

    public function store(StoreGalleryItemRequest $request): JsonResponse
    {
        $file = $request->file('image');
        $path = $file->store('gallery', 'public');

        $sortOrder = $request->input('sortOrder');
        if ($sortOrder === null || $sortOrder === '') {
            $max = (int) GalleryItem::query()->max('sort_order');
            $sortOrder = $max + 1;
        }

        $item = GalleryItem::query()->create([
            'path' => $path,
            'alt' => $request->input('alt'),
            'sort_order' => (int) $sortOrder,
        ]);

        return (new GalleryItemResource($item))->response()->setStatusCode(201);
    }

    public function update(UpdateGalleryItemRequest $request, GalleryItem $gallery_item): GalleryItemResource
    {
        $data = [];
        if ($request->has('alt')) {
            $data['alt'] = $request->input('alt');
        }
        if ($request->has('sortOrder')) {
            $data['sort_order'] = (int) $request->input('sortOrder');
        }
        if ($data !== []) {
            $gallery_item->update($data);
        }

        return new GalleryItemResource($gallery_item->fresh());
    }

    public function replaceImage(ReplaceGalleryImageRequest $request, GalleryItem $gallery_item): GalleryItemResource
    {
        $file = $request->file('image');
        $newPath = $file->store('gallery', 'public');

        $oldPath = $gallery_item->path;
        $gallery_item->update(['path' => $newPath]);

        $this->deleteStoredFileIfManaged($oldPath);

        return new GalleryItemResource($gallery_item->fresh());
    }

    public function destroy(DestroyGalleryItemRequest $request, GalleryItem $gallery_item): JsonResponse
    {
        $path = $gallery_item->path;
        $gallery_item->delete();

        $this->deleteStoredFileIfManaged($path);

        return response()->json(['ok' => true]);
    }

    private function deleteStoredFileIfManaged(string $path): void
    {
        if (str_starts_with($path, '/')) {
            return;
        }
        Storage::disk('public')->delete($path);
    }
}
