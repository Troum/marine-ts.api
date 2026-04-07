<?php

namespace App\DTO\Auth;

use App\Transformers\LowercaseTransformer;
use App\Transformers\TrimStringTransformer;
use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Attributes\Transform;
use Mycro\Core\Contracts\BaseDto;

final class LoginDto extends BaseDto
{
    #[MapProperty(from: ['username'], required: true)]
    #[Transform(LowercaseTransformer::class)]
    public readonly string $username;

    #[MapProperty(from: ['password'], required: true)]
    #[Transform(TrimStringTransformer::class)]
    public readonly string $password;
}
