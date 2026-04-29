<?php

namespace App\DTO\ContentPage;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateContentPageDto extends BaseDto
{
    #[MapProperty(from: ['slug'], required: false)]
    public readonly ?string $slug;

    #[MapProperty(from: ['isPublished', 'is_published'], required: false)]
    public readonly ?bool $is_published;

    #[MapProperty(from: ['sortOrder', 'sort_order'], required: false)]
    public readonly ?int $sort_order;

    #[MapProperty(from: ['showInquiryForm', 'show_inquiry_form'], required: false)]
    public readonly ?bool $show_inquiry_form;

    #[MapProperty(from: ['showPublicTitle', 'show_public_title'], required: false)]
    public readonly ?bool $show_public_title;

    /**
     * @var array<string, array<string, mixed>>|null
     */
    #[MapProperty(from: ['translations'], required: false)]
    public readonly ?array $translations;
}
