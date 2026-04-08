<?php

namespace App\Support;

final class MarineLocale
{
    /**
     * Resolve API content locale from ?locale= or Accept-Language.
     */
    public static function resolve(?string $queryLocale, ?string $acceptLanguage): string
    {
        $supported = config('marine.locales');
        if (! is_array($supported) || $supported === []) {
            return 'ru';
        }

        $q = $queryLocale !== null ? strtolower(trim($queryLocale)) : '';
        if ($q !== '' && in_array($q, $supported, true)) {
            return $q;
        }

        $fromHeader = self::fromAcceptLanguage($acceptLanguage ?? '', $supported);
        if ($fromHeader !== null) {
            return $fromHeader;
        }

        return (string) config('marine.default_locale');
    }

    /**
     * @param  list<string>|array<int, string>  $supported
     */
    private static function fromAcceptLanguage(string $header, array $supported): ?string
    {
        $header = trim($header);
        if ($header === '') {
            return null;
        }

        $parts = array_map('trim', explode(',', $header));
        foreach ($parts as $part) {
            $seg = strtolower(explode(';', $part, 2)[0]);
            $seg = str_replace('_', '-', $seg);
            if ($seg === '') {
                continue;
            }
            $primary = explode('-', $seg, 2)[0];
            foreach ($supported as $loc) {
                if ($loc === $seg || $loc === $primary) {
                    return $loc;
                }
            }
        }

        return null;
    }

    public static function isSupported(string $locale): bool
    {
        $supported = config('marine.locales');

        return is_array($supported) && in_array($locale, $supported, true);
    }
}
