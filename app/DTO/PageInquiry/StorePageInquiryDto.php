<?php

namespace App\DTO\PageInquiry;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StorePageInquiryDto extends BaseDto
{
    #[MapProperty(from: ['name'], required: true)]
    public readonly string $name;

    #[MapProperty(from: ['company'], required: true)]
    public readonly string $company;

    #[MapProperty(from: ['position'], required: false)]
    public readonly ?string $position;

    #[MapProperty(from: ['phone'], required: true)]
    public readonly string $phone;

    #[MapProperty(from: ['email'], required: true)]
    public readonly string $email;

    /**
     * Машинно-читаемые id выбранных типов судна (набор настраивается в CMS).
     *
     * @var list<string>
     */
    #[MapProperty(from: ['vessel_types'], required: true)]
    public readonly array $vessel_types;

    #[MapProperty(from: ['vessels_count'], required: true)]
    public readonly int $vessels_count;

    #[MapProperty(from: ['vessel_flag'], required: true)]
    public readonly string $vessel_flag;

    #[MapProperty(from: ['main_ports'], required: false)]
    public readonly ?string $main_ports;

    /**
     * Машинно-читаемые id выбранных услуг (набор настраивается в CMS).
     *
     * @var list<string>
     */
    #[MapProperty(from: ['required_services'], required: true)]
    public readonly array $required_services;

    #[MapProperty(from: ['message'], required: false)]
    public readonly ?string $message;

    #[MapProperty(from: ['source_page'], required: true)]
    public readonly string $source_page;
}
