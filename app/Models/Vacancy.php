<?php

namespace App\Models;

use App\Models\Concerns\ResolvesTranslations;
use App\Observers\VacancyObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ObservedBy([VacancyObserver::class])]
#[Fillable([
    'slug',
    'sort_order',
    'is_published',
])]
class Vacancy extends Model
{
    use ResolvesTranslations;
    use SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return HasMany<VacancyTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(VacancyTranslation::class);
    }

    public static function slugFromTitle(string $title): string
    {
        $slug = Str::slug($title);

        return $slug !== '' ? $slug : 'vacancy';
    }

    public static function ensureUniqueSlug(string $base, ?int $exceptId = null): string
    {
        $slug = $base;
        $counter = 2;
        while (static::query()
            ->when($exceptId !== null, fn (Builder $q) => $q->where('id', '!=', $exceptId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * @return HasMany<ApplicationForm, $this>
     */
    public function applicationForms(): HasMany
    {
        return $this->hasMany(ApplicationForm::class);
    }
}
