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

];
