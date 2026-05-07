<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Человекочитаемое название страницы-источника заявки (RU), по тем же slug, что в админке.
 */
final class PageInquirySourcePageLabel
{
    /**
     * @var array<string, string>
     */
    private const LABELS = [
        'home' => 'Главная',
        'about' => 'О компании',
        'services' => 'Судоремонт',
        'ship-management' => 'Судовой менеджмент',
        'crewing-management' => 'Крюинг-менеджмент',
        'lnk' => 'ЛНК',
        'contacts' => 'Контакты',
        'gallery' => 'Галерея',
        'projects' => 'Проекты',
        'news' => 'Новости',
        'vacancies' => 'Вакансии',
        'request' => 'Форма заявки',
        'privacy' => 'Политика конфиденциальности',
        'terms' => 'Условия использования',
    ];

    public static function resolve(string $sourcePage): string
    {
        $raw = trim($sourcePage);
        if ($raw === '') {
            return '—';
        }

        $segments = explode('/', $raw);
        $first = $segments[0] ?? '';
        $baseLabel = self::LABELS[$first] ?? Str::headline(str_replace(['-', '_'], ' ', $first));

        if (count($segments) < 2) {
            return $baseLabel;
        }

        $tail = trim(implode('/', array_slice($segments, 1)), '/');
        if ($tail === '') {
            return $baseLabel;
        }

        $tailPretty = Str::headline(str_replace(['/', '-', '_'], ' ', $tail));

        return $baseLabel.' — '.$tailPretty;
    }
}
