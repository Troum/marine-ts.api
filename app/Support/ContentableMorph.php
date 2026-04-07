<?php

namespace App\Support;

use App\Models\Project;
use App\Models\Service;
use InvalidArgumentException;

final class ContentableMorph
{
    /**
     * @return class-string
     */
    public static function classFromShort(string $short): string
    {
        return match ($short) {
            'service' => Service::class,
            'project' => Project::class,
            default => throw new InvalidArgumentException('Unknown contentable type: '.$short),
        };
    }

    public static function shortFromClass(?string $class): ?string
    {
        if ($class === null) {
            return null;
        }

        return match ($class) {
            Service::class => 'service',
            Project::class => 'project',
            default => null,
        };
    }
}
