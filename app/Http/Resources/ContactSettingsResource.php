<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{
 *     quick: list<array<string, mixed>>,
 *     departments: list<array<string, mixed>>,
 *     offices: list<array<string, mixed>>
 * } $resource
 */
class ContactSettingsResource extends JsonResource
{
    /**
     * @return array{quick: list<array<string, mixed>>, departments: list<array<string, mixed>>, offices: list<array<string, mixed>>}
     */
    public function toArray(Request $request): array
    {
        return [
            'quick' => $this->resource['quick'],
            'departments' => $this->resource['departments'],
            'offices' => $this->resource['offices'],
        ];
    }
}
