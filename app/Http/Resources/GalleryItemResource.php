<?php

namespace App\Http\Resources;

use App\Models\GalleryItem;
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
        $path = $this->path;
        $src = str_starts_with($path, '/')
            ? $path
            : URL::to(Storage::disk('public')->url($path));

        return [
            'id' => $this->id,
            'src' => $src,
            'alt' => $this->alt ?? '',
            'sortOrder' => $this->sort_order,
        ];
    }
}
