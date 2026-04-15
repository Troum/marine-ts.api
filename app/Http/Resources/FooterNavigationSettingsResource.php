<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{columns: list<array<string, mixed>>, legal: list<array<string, mixed>>} $resource
 */
class FooterNavigationSettingsResource extends JsonResource
{
    /**
     * @return array{columns: list<array<string, mixed>>, legal: list<array<string, mixed>>}
     */
    public function toArray(Request $request): array
    {
        return [
            'columns' => $this->resource['columns'],
            'legal' => $this->resource['legal'],
        ];
    }
}
