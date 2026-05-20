<?php

// Список Origin браузера (scheme + host + port). Без слэша в конце. Продакшен — через CORS_ALLOWED_ORIGINS в .env
$defaultOrigins = implode(',', [
    'http://localhost:3000',
    'http://localhost:3001',
    'http://127.0.0.1:3000',
    'http://127.0.0.1:3001',
    'https://marine-ts.mytests.space',
]);

$origins = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('CORS_ALLOWED_ORIGINS', $defaultOrigins)),
)));

if ($origins === []) {
    $origins = ['*'];
}

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | CORS_ALLOWED_ORIGINS: comma-separated list (e.g. Nuxt dev on :3000 while
    | the API is on another host like marine-ts.test). Add your production URL.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => $origins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        /*
         * Чтобы админка могла прочитать имя файла при скачивании PDF через fetch
         * (Content-Disposition недоступен кросс-домену без expose).
         */
        'Content-Disposition',
    ],

    'max_age' => 0,

    'supports_credentials' => false,

];
