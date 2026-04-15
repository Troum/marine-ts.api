<?php

namespace App\DTO\PageInquiry;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StorePageInquiryDto extends BaseDto
{
    #[MapProperty(from: ['name'], required: true)]
    public readonly string $name;

    #[MapProperty(from: ['email'], required: true)]
    public readonly string $email;

    #[MapProperty(from: ['phone'], required: false)]
    public readonly ?string $phone;

    #[MapProperty(from: ['company'], required: false)]
    public readonly ?string $company;

    #[MapProperty(from: ['vessel_name'], required: false)]
    public readonly ?string $vessel_name;

    #[MapProperty(from: ['imo'], required: false)]
    public readonly ?string $imo;

    #[MapProperty(from: ['message'], required: true)]
    public readonly string $message;

    #[MapProperty(from: ['source_page'], required: true)]
    public readonly string $source_page;
}
