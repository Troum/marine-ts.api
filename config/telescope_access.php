<?php

/**
 * Доступ к дашборду Laravel Telescope (gate viewTelescope и форма /telescope-auth/login).
 *
 * Список email через запятую в .env: TELESCOPE_DASHBOARD_EMAILS=a@b.com,c@d.com
 *
 * TELESCOPE_RECORD_ALL=true — сохранять в БД все записи Telescope (включая успешные HTTP).
 * По умолчанию false: вне local пишутся только ошибки/исключения и т.д. (см. TelescopeServiceProvider).
 */
return [
    'record_all' => filter_var(env('TELESCOPE_RECORD_ALL', false), FILTER_VALIDATE_BOOL),

    'dashboard_emails' => array_values(array_filter(array_map(
        static fn (string $e): string => strtolower(trim($e)),
        explode(',', (string) env('TELESCOPE_DASHBOARD_EMAILS', 'troum@outlook.com')),
    ))),
];
