<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title',
    'description',
    'features',
    'icon_key',
    'sort_order',
    'seo_title',
    'seo_description',
    'seo_keywords',
])]
class Service extends Model
{
    use SoftDeletes;

    /**
     * @return MorphOne<ContentPage, $this>
     */
    public function contentPage(): MorphOne
    {
        return $this->morphOne(ContentPage::class, 'contentable');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'features' => 'array',
            'sort_order' => 'integer',
        ];
    }
}
