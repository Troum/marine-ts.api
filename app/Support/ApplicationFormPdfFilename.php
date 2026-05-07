<?php

namespace App\Support;

use App\Models\ApplicationForm;
use Illuminate\Support\Str;

/**
 * Имя PDF: {last}_{first}_pos_{должности...}_ship_{типы_судов...}_{shortUuid}.pdf (латиница, подчёркивания).
 * Несколько должностей и типов судов перечисляются отдельными сегментами, без placeholder «Multi».
 */
final class ApplicationFormPdfFilename
{
    public static function build(ApplicationForm $form): string
    {
        $payload = is_array($form->payload) ? $form->payload : [];

        $ln = self::segment(self::str($payload, 'lastName'));
        $fn = self::segment(self::str($payload, 'firstName'));

        $positionStrings = self::stringListFromPayload($payload, 'positionApplyingFor', 'position_applying_for');
        $vesselStrings = self::stringListFromPayload($payload, 'desiredVesselTypes', 'desired_vessel_types');

        $positionSlugs = self::segmentsFromList($positionStrings);
        if ($positionSlugs === []) {
            $positionSlugs = ['X'];
        }

        $vesselSlugs = self::segmentsFromList($vesselStrings);
        if ($vesselSlugs === []) {
            $vesselSlugs = [self::segment('Unknown')];
        }

        if (! is_string($form->uuid) || $form->uuid === '') {
            throw new \RuntimeException('ApplicationForm.uuid is required for PDF filename.');
        }
        $short = strtolower(substr(str_replace('-', '', $form->uuid), 0, 8));

        $parts = array_merge(
            [$ln, $fn, 'pos'],
            $positionSlugs,
            ['ship'],
            $vesselSlugs,
            [$short],
        );
        $parts = array_values(array_filter($parts, static fn (string $s): bool => $s !== ''));
        $base = implode('_', $parts);

        return $base.'.pdf';
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
     * @param  list<string>  $strings
     * @return list<string>
     */
    private static function segmentsFromList(array $strings): array
    {
        $out = [];
        foreach ($strings as $s) {
            $out[] = self::segment($s);
        }

        return array_values(array_unique(array_filter($out, static fn (string $s): bool => $s !== '')));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function str(array $payload, string $camelKey): string
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

    private static function segment(string $raw): string
    {
        $t = trim($raw);
        if ($t === '') {
            return 'X';
        }
        $slug = Str::slug($t, '_', 'ru');
        $slug = str_replace('-', '_', $slug);
        $slug = preg_replace('/[^A-Za-z0-9_]/', '', $slug) ?? '';
        $slug = preg_replace('/_+/', '_', $slug) ?? '';
        $slug = trim($slug, '_');

        return $slug !== '' ? $slug : 'X';
    }
}
