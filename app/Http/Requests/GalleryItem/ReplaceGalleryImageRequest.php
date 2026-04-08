<?php

namespace App\Http\Requests\GalleryItem;

use App\Models\GalleryItem;
use Illuminate\Foundation\Http\FormRequest;

class ReplaceGalleryImageRequest extends FormRequest
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
            'image' => ['required', 'file', 'image', 'max:20480'],
        ];
    }
}
