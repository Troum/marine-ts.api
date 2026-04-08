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

];
