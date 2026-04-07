<?php

namespace App\Support;

use Illuminate\Http\Request;

final class AdminListQuery
{
    /**
     * @param  list<string>  $allowedColumns
     * @return array{order_column: string, order_direction: string}
     */
    public static function sortOrder(
        Request $request,
        array $allowedColumns,
        string $defaultColumn = 'id',
        string $defaultDirection = 'desc',
    ): array {
        $sort = (string) $request->query('sort', $defaultColumn);
        if (! in_array($sort, $allowedColumns, true)) {
            $sort = $defaultColumn;
        }
        $order = strtolower((string) $request->query('order', $defaultDirection));
        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = in_array($defaultDirection, ['asc', 'desc'], true) ? $defaultDirection : 'desc';
        }

        return [
            'order_column' => $sort,
            'order_direction' => $order,
        ];
    }

    public static function search(Request $request): ?string
    {
        $q = trim((string) $request->query('search', ''));
        if ($q === '') {
            return null;
        }

        return mb_substr($q, 0, 500);
    }

    /** SQL LIKE pattern with % wildcards, special chars escaped. */
    public static function likePattern(string $search): string
    {
        $s = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);

        return '%'.$s.'%';
    }

    /**
     * Query param `published`: 1 = only published, 0 = only draft, omit = all.
     */
    public static function publishedTriState(Request $request): ?bool
    {
        if (! $request->has('published')) {
            return null;
        }
        $v = $request->query('published');
        if ($v === '1' || $v === 1 || $v === true) {
            return true;
        }
        if ($v === '0' || $v === 0 || $v === false) {
            return false;
        }

        return null;
    }

    /**
     * Query param `read`: 1 = read only, 0 = unread only, omit = all.
     */
    public static function readTriState(Request $request): ?bool
    {
        if (! $request->has('read')) {
            return null;
        }
        $v = $request->query('read');
        if ($v === '1' || $v === 1 || $v === true) {
            return true;
        }
        if ($v === '0' || $v === 0 || $v === false) {
            return false;
        }

        return null;
    }

    /**
     * Must match ApplicationFormStatus::values() value or null.
     */
    public static function statusFilter(Request $request): ?string
    {
        $s = (string) $request->query('status', '');
        if ($s === '') {
            return null;
        }

        return in_array($s, \App\Enums\ApplicationFormStatus::values(), true) ? $s : null;
    }
}
