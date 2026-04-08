<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'locale',
    'label',
    'seo_title',
    'seo_description',
    'seo_keywords',
])]
class SiteSeoPageTranslation extends Model
{
    /**
     * @return BelongsTo<SiteSeoPage, $this>
     */
    public function siteSeoPage(): BelongsTo
    {
        return $this->belongsTo(SiteSeoPage::class);
    }
}
