<?php

namespace App\Support;

use App\Models\ApplicationForm;
use Illuminate\Support\Str;

/**
 * Имя PDF: {должности}_{фамилия}_{имя}_{типы_судов}.pdf — латиница, слова с заглавной буквы, через подчёркивание.
 * Пример: Master_Chief_Officer_Ivanov_Petr_Tanker_Dry_Cargo.pdf
 */
final class ApplicationFormPdfFilename
{
    public static function build(ApplicationForm $form): string
    {
        $payload = is_array($form->payload) ? $form->payload : [];

        $positionStrings = self::stringListFromPayload($payload, 'positionApplyingFor', 'position_applying_for');
        $vesselStrings = self::stringListFromPayload($payload, 'desiredVesselTypes', 'desired_vessel_types');

        $positionsSegment = self::buildEnumeratedCapitalized($positionStrings, 'Unknown');

        $ln = self::capitalizedTranslitWords(self::scalarStr($payload, 'lastName'));
        $fn = self::capitalizedTranslitWords(self::scalarStr($payload, 'firstName'));
        $nameParts = array_values(array_filter([$ln, $fn], static fn (string $s): bool => $s !== ''));
        $nameSegment = $nameParts === [] ? 'Unknown' : implode('_', $nameParts);

        $vesselsSegment = self::buildEnumeratedCapitalized($vesselStrings, 'Unknown');

        $base = $positionsSegment.'_'.$nameSegment.'_'.$vesselsSegment;
        $base = preg_replace('/[^A-Za-z0-9_]/', '', $base) ?? '';
        $base = preg_replace('/_+/', '_', $base) ?? '';
        $base = trim($base, '_');

        return ($base !== '' ? $base : 'Application').'.pdf';
    }

    /**
     * Несколько строк из анкеты: каждая транслитерируется и переводится в Word_Case, затем все части перечисляются через «_».
     *
     * @param  list<string>  $strings
     */
    private static function buildEnumeratedCapitalized(array $strings, string $ifEmpty): string
    {
        $chunks = [];
        foreach ($strings as $s) {
            $c = self::capitalizedTranslitWords($s);
            if ($c !== '') {
                $chunks[] = $c;
            }
        }
        $chunks = array_values(array_unique($chunks));

        return $chunks === [] ? $ifEmpty : implode('_', $chunks);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return list<string>
     */
    private static function stringListFromPayload(array $payload, string $camelKey, string $snakeKey): array
    {
        $raw = $payload[$camelKey] ?? $payload[$snakeKey] ?? null;
        if (is_array($raw)) {
            $list = [];
            foreach ($raw as $item) {
                if (is_string($item)) {
                    $t = trim($item);
                    if ($t !== '') {
                        $list[] = $t;
                    }
                }
            }

            return array_values(array_unique($list));
        }
        if (is_scalar($raw)) {
            $t = trim((string) $raw);

            return $t !== '' ? [$t] : [];
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function scalarStr(array $payload, string $camelKey): string
    {
        if (isset($payload[$camelKey]) && is_scalar($payload[$camelKey])) {
            return trim((string) $payload[$camelKey]);
        }
        $snake = Str::snake($camelKey);
        if (isset($payload[$snake]) && is_scalar($payload[$snake])) {
            return trim((string) $payload[$snake]);
        }

        return '';
    }

    /**
     * Транслит + slug (латиница, «слова» через «_»), затем каждое слово с заглавной буквы: Master_Chief_Officer.
     */
    private static function capitalizedTranslitWords(string $raw): string
    {
        $slug = self::toTranslitSlug($raw);
        if ($slug === '') {
            return '';
        }
        $parts = explode('_', $slug);
        $out = [];
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $first = Str::upper(Str::substr($part, 0, 1));
            $rest = Str::lower(Str::substr($part, 1));
            $out[] = $first.$rest;
        }

        return implode('_', $out);
    }

    private static function toTranslitSlug(string $raw): string
    {
        $t = trim($raw);
        if ($t === '') {
            return '';
        }
        $slug = Str::slug($t, '_', 'ru');
        $slug = str_replace('-', '_', $slug);
        $slug = preg_replace('/_+/', '_', $slug) ?? '';

        return trim($slug, '_');
    }
}
