<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'locale',
    'title',
    'excerpt',
    'content',
    'category',
    'seo_title',
    'seo_description',
    'seo_keywords',
    'seo_image',
])]
class NewsTranslation extends Model
{
    /**
     * @return BelongsTo<News, $this>
     */
    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
