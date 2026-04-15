<?php

namespace App\DTO\Analytics;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class RecordPageViewDto extends BaseDto
{
    #[MapProperty(from: ['path'], required: true)]
    public readonly string $path;

    #[MapProperty(from: ['title'], required: false)]
    public readonly ?string $title;

    #[MapProperty(from: ['referrer'], required: false)]
    public readonly ?string $referrer;
}
