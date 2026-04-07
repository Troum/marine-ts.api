<?php

namespace App\Transformers;

use Mycro\Core\Contracts\TransformerContract;

final class LowercaseTransformer implements TransformerContract
{
    public function transform(mixed $value): mixed
    {
        return is_string($value) ? strtolower($value) : $value;
    }
}
