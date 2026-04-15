<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{name: string, label: string} $resource
 */
class RoleCatalogEntryResource extends JsonResource
{
    /**
     * @return array{name: string, label: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->resource['name'],
            'label' => $this->resource['label'],
        ];
    }
}
