<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{theme: string, hiddenSections: array<string, bool>} $resource
 */
class AppearanceSettingsResource extends JsonResource
{
    /**
     * @return array{theme: string, hiddenSections: array<string, bool>}
     */
    public function toArray(Request $request): array
    {
        return [
            'theme' => $this->resource['theme'],
            'hiddenSections' => $this->resource['hiddenSections'] ?? [],
        ];
    }
}
