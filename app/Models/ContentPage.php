<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'slug',
    'title',
    'excerpt',
    'body',
    'is_published',
    'sort_order',
    'seo_title',
    'seo_description',
    'seo_keywords',
    'contentable_type',
    'contentable_id',
])]
class ContentPage extends Model
{
    use SoftDeletes;

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
        ];
    }
}
