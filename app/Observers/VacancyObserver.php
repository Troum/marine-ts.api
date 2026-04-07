<?php

namespace App\Observers;

use App\Models\Vacancy;
use Illuminate\Support\Str;

class VacancyObserver
{
    public function saving(Vacancy $vacancy): void
    {
        if (blank($vacancy->slug)) {
            $base = Vacancy::slugFromTitle($vacancy->title);
        } else {
            $base = Str::slug($vacancy->slug);
            if ($base === '') {
                $base = Vacancy::slugFromTitle($vacancy->title);
            }
        }

        $vacancy->slug = Vacancy::ensureUniqueSlug($base, $vacancy->exists ? $vacancy->id : null);
    }
}
