<?php

/**
 * Доступ к дашборду Laravel Telescope (gate viewTelescope и форма /telescope-auth/login).
 *
 * Список email через запятую в .env: TELESCOPE_DASHBOARD_EMAILS=a@b.com,c@d.com
 */
return [
    'dashboard_emails' => array_values(array_filter(array_map(
        static fn (string $e): string => strtolower(trim($e)),
        explode(',', (string) env('TELESCOPE_DASHBOARD_EMAILS', 'troum@outlook.com')),
    ))),
];
