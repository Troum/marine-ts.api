<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Форматирует строковое поле даты для публичного API с учётом локали запроса.
 * Поддерживает: год (2024), ISO YYYY-MM-DD; иное — возвращается как есть (legacy).
 */
final class LocalizedDisplayDate
{
    public static function format(?string $raw, string $targetLocale): string
    {
        if ($raw === null || $raw === '') {
            return '';
        }
        $raw = trim($raw);
        if (preg_match('/^\d{4}$/', $raw)) {
            return $raw;
        }
        if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $raw, $m)) {
            try {
                $d = Carbon::parse($m[1])->startOfDay();

                return $d->locale($targetLocale)->translatedFormat('j F Y');
            } catch (\Throwable) {
                return $raw;
            }
        }

        return $raw;
    }
}
