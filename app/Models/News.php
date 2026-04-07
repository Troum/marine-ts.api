<?php

namespace App\Models;

use App\Observers\NewsObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ObservedBy([NewsObserver::class])]
#[Table(name: 'news')]
#[Fillable([
    'slug',
    'title',
    'excerpt',
    'content',
    'date',
    'author',
    'category',
    'featured',
    'image',
    'seo_title',
    'seo_description',
    'seo_keywords',
])]
class News extends Model
{
    use SoftDeletes;

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
        ];
    }

    public static function slugFromTitle(string $title): string
    {
        $slug = Str::slug($title);

        return $slug !== '' ? $slug : 'news';
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
}
