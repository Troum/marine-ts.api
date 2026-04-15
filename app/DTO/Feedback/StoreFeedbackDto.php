<?php

namespace App\DTO\Feedback;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreFeedbackDto extends BaseDto
{
    #[MapProperty(from: ['name'], required: true)]
    public readonly string $name;

    #[MapProperty(from: ['email'], required: true)]
    public readonly string $email;

    #[MapProperty(from: ['phone'], required: false)]
    public readonly ?string $phone;

    #[MapProperty(from: ['message'], required: true)]
    public readonly string $message;
}
