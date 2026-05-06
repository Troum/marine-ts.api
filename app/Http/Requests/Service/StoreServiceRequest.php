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

    protected function prepareForValidation(): void
    {
        $raw = $this->input('translations');
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->merge(['translations' => $decoded]);
            }
        }

        $default = (string) config('marine.default_locale');
        if (! $this->has('translations') && $this->has('title')) {
            $this->merge([
                'translations' => [
                    $default => [
                        'title' => $this->input('title'),
                        'description' => $this->input('description'),
                        'features' => $this->input('features'),
                        'seoTitle' => $this->input('seoTitle') ?? $this->input('seo_title'),
                        'seoDescription' => $this->input('seoDescription') ?? $this->input('seo_description'),
                        'seoKeywords' => $this->input('seoKeywords') ?? $this->input('seo_keywords'),
                        'seoImage' => $this->input('seoImage') ?? $this->input('seo_image'),
                    ],
                ],
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $locales = config('marine.locales');
        if (! is_array($locales)) {
            $locales = ['ru', 'en'];
        }
        $default = (string) config('marine.default_locale');

        $rules = [
            'iconKey' => ['required', 'string', 'max:64'],
            'sortOrder' => ['sometimes', 'integer', 'min:0', 'max:999999'],
            'translations' => ['required', 'array'],
            'image' => ['sometimes', 'nullable', 'file', 'image', 'max:20480'],
        ];

        foreach ($locales as $loc) {
            $isDefault = $loc === $default;
            $prefix = "translations.$loc";
            $rules[$prefix] = [$isDefault ? 'required' : 'nullable', 'array'];
            $rules["$prefix.title"] = [$isDefault ? 'required' : 'sometimes', 'nullable', 'string', 'max:500'];
            $rules["$prefix.description"] = [$isDefault ? 'required' : 'sometimes', 'nullable', 'string'];
            $rules["$prefix.features"] = $isDefault
                ? ['required', 'array', 'min:1']
                : ['sometimes', 'nullable', 'array'];
            $rules["$prefix.features.*"] = ['string', 'max:500'];
            $rules["$prefix.seoTitle"] = ['sometimes', 'nullable', 'string', 'max:255'];
            $rules["$prefix.seoDescription"] = ['sometimes', 'nullable', 'string', 'max:8000'];
            $rules["$prefix.seoKeywords"] = ['sometimes', 'nullable', 'string', 'max:500'];
            $rules["$prefix.seoImage"] = ['sometimes', 'nullable', 'string', 'max:2000'];
        }

        return $rules;
    }
}
