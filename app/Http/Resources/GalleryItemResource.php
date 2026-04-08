<?php

namespace App\Http\Resources;

use App\Models\GalleryItem;
use App\Support\ApiTranslationPayload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * @mixin GalleryItem
 */
class GalleryItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var GalleryItem $item */
        $item = $this->resource;
        $locale = app()->getLocale();
        $t = $item->translationForLocale($locale);

        $path = $this->path;
        $src = str_starts_with($path, '/')
            ? $path
            : URL::to(Storage::disk('public')->url($path));

        $data = [
            'id' => $this->id,
            'src' => $src,
            'alt' => $t?->alt ?? '',
            'sortOrder' => $this->sort_order,
        ];

        if (ApiTranslationPayload::wantsFullTranslations($request)) {
            $item->loadMissing('translations');
            $map = [];
            foreach ($item->translations as $tr) {
                $map[$tr->locale] = ['alt' => $tr->alt];
            }
            $data['translations'] = $map;
        }

        return $data;
    }
}
