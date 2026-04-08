<?php

namespace App\Http\Requests\GalleryItem;

use App\Models\GalleryItem;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var GalleryItem $item */
        $item = $this->route('gallery_item');

        return $this->user()->can('update', $item);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'alt' => ['sometimes', 'nullable', 'string', 'max:500'],
            'sortOrder' => ['sometimes', 'integer', 'min:0', 'max:999999'],
        ];
    }
}
