<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array<string, mixed> $resource
 */
class NavigationSettingsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $r = $this->resource;
        $out = [
            'main' => $r['main'],
            'more' => $r['more'],
            'menuVariant' => $r['menuVariant'],
            'menuFontSize' => $r['menuFontSize'],
            'menuFontWeight' => $r['menuFontWeight'],
            'menuTextCase' => $r['menuTextCase'],
            'menuJustify' => $r['menuJustify'],
        ];
        foreach (['menuItemHoverColor', 'menuItemColor', 'horizItems', 'burgerContacts'] as $key) {
            if (array_key_exists($key, $r)) {
                $out[$key] = $r[$key];
            }
        }

        return $out;
    }
}
