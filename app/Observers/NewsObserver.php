<?php

namespace App\Observers;

use App\Models\News;
use Illuminate\Support\Str;

class NewsObserver
{
    public function saving(News $news): void
    {
        if (! filled($news->slug)) {
            return;
        }

        $base = Str::slug($news->slug);
        if ($base === '') {
            return;
        }

        $news->slug = News::ensureUniqueSlug($base, $news->exists ? $news->id : null);
    }
}
