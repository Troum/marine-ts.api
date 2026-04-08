<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'locale',
    'title',
    'excerpt',
    'body',
    'seo_title',
    'seo_description',
    'seo_keywords',
])]
class ContentPageTranslation extends Model
{
    /**
     * @return BelongsTo<ContentPage, $this>
     */
    public function contentPage(): BelongsTo
    {
        return $this->belongsTo(ContentPage::class);
    }
}
