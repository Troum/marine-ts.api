<?php

namespace App\Http\Requests\Service;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Service $service */
        $service = $this->route('service');

        return $this->user()->can('update', $service);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:500'],
            'description' => ['sometimes', 'string'],
            'features' => ['sometimes', 'array', 'min:1'],
            'features.*' => ['string', 'max:500'],
            'iconKey' => ['sometimes', 'string', 'max:64'],
            'sortOrder' => ['sometimes', 'integer', 'min:0', 'max:999999'],
            'seoTitle' => ['sometimes', 'nullable', 'string', 'max:255'],
            'seoDescription' => ['sometimes', 'nullable', 'string', 'max:8000'],
            'seoKeywords' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}
