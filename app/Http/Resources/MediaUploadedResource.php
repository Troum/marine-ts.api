<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{url: string} $resource
 */
class MediaUploadedResource extends JsonResource
{
    /** Загрузчик в админке читает `url` из корня ответа. */
    public static $wrap = null;

    /**
     * @return array{url: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'url' => $this->resource['url'],
        ];
    }
}
