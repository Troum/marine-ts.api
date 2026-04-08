<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * When true, JSON resources should expose the full translations map for admin/editing.
 */
final class ApiTranslationPayload
{
    public static function wantsFullTranslations(Request $request): bool
    {
        if (! $request->user()) {
            return false;
        }

        $path = '/'.$request->path();

        if (str_contains($path, '/manage')) {
            return true;
        }

        if (preg_match('#/(news|vacancies|projects|services|content-pages|gallery)/\d+(\?|$)#', $path) === 1) {
            return true;
        }

        if (str_contains($path, '/seo/pages/')) {
            return true;
        }

        return false;
    }
}
