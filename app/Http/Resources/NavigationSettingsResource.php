<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{main: list<array<string, mixed>>, more: list<array<string, mixed>>} $resource
 */
class NavigationSettingsResource extends JsonResource
{
    /**
     * @return array{main: list<array<string, mixed>>, more: list<array<string, mixed>>}
     */
    public function toArray(Request $request): array
    {
        return [
            'main' => $this->resource['main'],
            'more' => $this->resource['more'],
        ];
    }
}
