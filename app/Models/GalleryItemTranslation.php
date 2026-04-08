<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'locale',
    'alt',
])]
class GalleryItemTranslation extends Model
{
    /**
     * @return BelongsTo<GalleryItem, $this>
     */
    public function galleryItem(): BelongsTo
    {
        return $this->belongsTo(GalleryItem::class);
    }
}
