<?php

namespace App\DTO\ApplicationForm;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreOpenApplicationFormDto extends BaseDto
{
    #[MapProperty(from: ['lastName'], required: true)]
    public readonly string $lastName;

    #[MapProperty(from: ['firstName'], required: true)]
    public readonly string $firstName;

    #[MapProperty(from: ['email'], required: true)]
    public readonly string $email;

    #[MapProperty(from: ['mobilePhone'], required: true)]
    public readonly string $mobilePhone;

    #[MapProperty(from: ['consentRuAccuracy'], required: true)]
    public readonly bool $consentRuAccuracy;

    #[MapProperty(from: ['consentRuPd'], required: true)]
    public readonly bool $consentRuPd;

    #[MapProperty(from: ['consentEnAccuracy'], required: true)]
    public readonly bool $consentEnAccuracy;

    #[MapProperty(from: ['consentEnPd'], required: true)]
    public readonly bool $consentEnPd;

    /**
     * Тело анкеты в формате API (camelCase), как после валидации запроса.
     *
     * @return array<string, mixed>
     */
    public function toPayloadArray(): array
    {
        return [
            'lastName' => $this->lastName,
            'firstName' => $this->firstName,
            'email' => $this->email,
            'mobilePhone' => $this->mobilePhone,
            'consentRuAccuracy' => $this->consentRuAccuracy,
            'consentRuPd' => $this->consentRuPd,
            'consentEnAccuracy' => $this->consentEnAccuracy,
            'consentEnPd' => $this->consentEnPd,
        ];
    }
}
