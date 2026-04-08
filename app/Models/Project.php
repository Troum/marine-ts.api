<?php

namespace App\Models;

use App\Models\Concerns\ResolvesTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'type',
    'date',
    'image',
])]
class Project extends Model
{
    use ResolvesTranslations;
    use SoftDeletes;

    /**
     * @return HasMany<ProjectTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ProjectTranslation::class);
    }

    /**
     * @return MorphOne<ContentPage, $this>
     */
    public function contentPage(): MorphOne
    {
        return $this->morphOne(ContentPage::class, 'contentable');
    }
}
