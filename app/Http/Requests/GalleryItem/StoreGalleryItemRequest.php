<?php

namespace App\Http\Requests\GalleryItem;

use App\Models\GalleryItem;
use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', GalleryItem::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'image' => ['required', 'file', 'image', 'max:20480'],
            'alt' => ['nullable', 'string', 'max:500'],
            'sortOrder' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'translations' => ['sometimes', 'array'],
        ];
    }
}
