<?php

namespace App\Support;

/**
 * Подстановка подписей из CMS/формы вместо машинных id в письмах и отчётах.
 */
final class PageInquiryLabelResolver
{
    /**
     * @param  list<string>|list<int>|null  $ids
     * @param  array<string, mixed>|null  $labelMap  id → подпись
     * @return list<string>
     */
    public static function resolveList(?array $ids, ?array $labelMap): array
    {
        if (! is_array($ids) || $ids === []) {
            return [];
        }

        $map = is_array($labelMap) ? $labelMap : [];

        $out = [];
        foreach ($ids as $id) {
            $idStr = is_string($id) ? $id : (string) $id;
            $label = self::pickLabel($map, $idStr);
            $out[] = $label ?? $idStr;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $map
     */
    private static function pickLabel(array $map, string $idStr): ?string
    {
        foreach ([$idStr, strtolower($idStr), strtoupper($idStr)] as $key) {
            if (! array_key_exists($key, $map)) {
                continue;
            }
            $raw = $map[$key];
            if (is_string($raw)) {
                $t = trim($raw);

                return $t !== '' ? $t : null;
            }
            if (is_scalar($raw) && (string) $raw !== '') {
                return trim((string) $raw);
            }
        }

        return null;
    }
}
