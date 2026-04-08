<?php

namespace App\Models;

use App\Models\Concerns\ResolvesTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
])]
class SiteSeoPage extends Model
{
    use ResolvesTranslations;

    /**
     * @return HasMany<SiteSeoPageTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SiteSeoPageTranslation::class);
    }
}
