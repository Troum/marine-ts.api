<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content locales (dynamic translations in DB)
    |--------------------------------------------------------------------------
    |
    | First locale is the primary storage locale for legacy single-locale payloads.
    |
    */

    'locales' => ['ru', 'en'],

    'default_locale' => env('MARINE_DEFAULT_LOCALE', 'ru'),

    'fallback_locale' => env('MARINE_FALLBACK_LOCALE', 'ru'),

    /*
    |--------------------------------------------------------------------------
    | Письма (HTML): логотип с публичного сайта
    |--------------------------------------------------------------------------
    |
    | Полный URL картинки. По умолчанию: FRONTEND_URL + /logo-red.svg (как в Nuxt public).
    |
    */

    'mail_logo_url' => env('MARINE_MAIL_LOGO_URL') ?: (filled(env('FRONTEND_URL')) ? rtrim((string) env('FRONTEND_URL'), '/').'/logo-red.svg' : null),

    'mail_site_url' => env('MARINE_MAIL_SITE_URL') ?: (filled(env('FRONTEND_URL')) ? rtrim((string) env('FRONTEND_URL'), '/') : null),

    /*
    |--------------------------------------------------------------------------
    | Оптимизация изображений при загрузке в public/media (jpegoptim, optipng, cwebp)
    |--------------------------------------------------------------------------
    |
    | Требуются системные пакеты: jpegoptim, optipng, webp (cwebp).
    | Если утилита не установлена, шаг пропускается без ошибки загрузки.
    |
    */

    'media_image_optimize' => [
        'enabled' => env('MEDIA_IMAGE_OPTIMIZE', true),
        'jpeg_max_quality' => (int) env('MEDIA_IMAGE_JPEG_MAX_QUALITY', 85),
        'strip_exif' => env('MEDIA_IMAGE_STRIP_EXIF', true),
        'png_level' => (int) env('MEDIA_IMAGE_PNG_LEVEL', 2),
        'generate_webp' => env('MEDIA_IMAGE_GENERATE_WEBP', true),
        'webp_quality' => (int) env('MEDIA_IMAGE_WEBP_QUALITY', 82),
    ],

];
