<?php

namespace App\Http\Requests\Service;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Service::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'features' => ['required', 'array', 'min:1'],
            'features.*' => ['string', 'max:500'],
            'iconKey' => ['required', 'string', 'max:64'],
            'sortOrder' => ['sometimes', 'integer', 'min:0', 'max:999999'],
            'seoTitle' => ['nullable', 'string', 'max:255'],
            'seoDescription' => ['nullable', 'string', 'max:8000'],
            'seoKeywords' => ['nullable', 'string', 'max:500'],
        ];
    }
}
