<?php

namespace App\Observers;

use App\Models\News;
use Illuminate\Support\Str;

class NewsObserver
{
    public function saving(News $news): void
    {
        if (blank($news->slug)) {
            $base = News::slugFromTitle($news->title);
        } else {
            $base = Str::slug($news->slug);
            if ($base === '') {
                $base = News::slugFromTitle($news->title);
            }
        }

        $news->slug = News::ensureUniqueSlug($base, $news->exists ? $news->id : null);
    }
}
