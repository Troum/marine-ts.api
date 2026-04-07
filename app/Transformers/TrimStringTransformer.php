<?php

namespace App\Transformers;

use Mycro\Core\Contracts\TransformerContract;

final class TrimStringTransformer implements TransformerContract
{
    public function transform(mixed $value): mixed
    {
        return is_string($value) ? trim($value) : $value;
    }
}
