<?php

namespace App\Observers;

use App\Models\Vacancy;
use Illuminate\Support\Str;

class VacancyObserver
{
    public function saving(Vacancy $vacancy): void
    {
        if (! filled($vacancy->slug)) {
            return;
        }

        $base = Str::slug($vacancy->slug);
        if ($base === '') {
            return;
        }

        $vacancy->slug = Vacancy::ensureUniqueSlug($base, $vacancy->exists ? $vacancy->id : null);
    }
}
