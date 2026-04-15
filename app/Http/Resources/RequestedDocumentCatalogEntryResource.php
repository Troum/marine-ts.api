<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{key: string, label: string, group: string} $resource
 */
class RequestedDocumentCatalogEntryResource extends JsonResource
{
    /**
     * @return array{key: string, label: string, group: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->resource['key'],
            'label' => $this->resource['label'],
            'group' => $this->resource['group'],
        ];
    }
}
