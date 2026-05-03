<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{theme: string} $resource
 */
class AppearanceSettingsResource extends JsonResource
{
    /**
     * @return array{theme: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'theme' => $this->resource['theme'],
        ];
    }
}
