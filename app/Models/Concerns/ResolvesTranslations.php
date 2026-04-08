<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

trait ResolvesTranslations
{
    /**
     * @phpstan-return Model|null
     */
    public function translationForLocale(string $locale): ?Model
    {
        $fallback = (string) config('marine.fallback_locale');

        if ($this->relationLoaded('translations')) {
            return $this->pickTranslation($this->translations, $locale, $fallback);
        }

        /** @var HasMany $hasMany */
        $hasMany = $this->translations();
        $t = $hasMany->where('locale', $locale)->first();
        if ($t !== null) {
            return $t;
        }
        if ($locale !== $fallback) {
            $t = $hasMany->where('locale', $fallback)->first();
            if ($t !== null) {
                return $t;
            }
        }

        return $hasMany->first();
    }

    /**
     * @param Collection<int, Model> $translations
     */
    private function pickTranslation(Collection $translations, string $locale, string $fallback): ?Model
    {
        $t = $translations->firstWhere('locale', $locale);
        if ($t !== null) {
            return $t;
        }
        if ($locale !== $fallback) {
            $t = $translations->firstWhere('locale', $fallback);
            if ($t !== null) {
                return $t;
            }
        }

        return $translations->first();
    }
}
