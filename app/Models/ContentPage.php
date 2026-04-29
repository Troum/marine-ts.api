<?php

namespace App\Models;

use App\Models\Concerns\ResolvesTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'slug',
    'is_published',
    'sort_order',
    'show_inquiry_form',
    'show_public_title',
    'contentable_type',
    'contentable_id',
])]
class ContentPage extends Model
{
    use ResolvesTranslations;
    use SoftDeletes;

    /**
     * @return HasMany<ContentPageTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ContentPageTranslation::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function contentable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
            'show_inquiry_form' => 'boolean',
            'show_public_title' => 'boolean',
        ];
    }
}
