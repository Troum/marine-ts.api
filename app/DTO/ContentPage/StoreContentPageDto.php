<?php

namespace App\DTO\ContentPage;

use Mycro\Core\Attributes\DefaultValue;
use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreContentPageDto extends BaseDto
{
    #[MapProperty(from: ['slug'], required: true)]
    public readonly string $slug;

    #[MapProperty(from: ['isPublished', 'is_published'], required: false)]
    #[DefaultValue(true)]
    public readonly bool $is_published;

    #[MapProperty(from: ['sortOrder', 'sort_order'], required: false)]
    #[DefaultValue(0)]
    public readonly int $sort_order;

    #[MapProperty(from: ['showInquiryForm', 'show_inquiry_form'], required: false)]
    #[DefaultValue(false)]
    public readonly bool $show_inquiry_form;

    /**
     * @var array<string, array<string, mixed>>
     */
    #[MapProperty(from: ['translations'], required: true)]
    public readonly array $translations;
}
