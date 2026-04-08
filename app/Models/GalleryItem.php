<?php

namespace App\Models;

use App\Models\Concerns\ResolvesTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'path',
    'sort_order',
])]
class GalleryItem extends Model
{
    use ResolvesTranslations;

    /**
     * @return HasMany<GalleryItemTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(GalleryItemTranslation::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
