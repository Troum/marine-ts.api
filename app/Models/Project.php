<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title',
    'type',
    'type_label',
    'location',
    'date',
    'description',
    'stats',
    'image',
    'seo_title',
    'seo_description',
    'seo_keywords',
])]
class Project extends Model
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
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'stats' => 'array',
        ];
    }
}
