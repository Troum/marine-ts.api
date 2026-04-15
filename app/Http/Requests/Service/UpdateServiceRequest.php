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

    protected function prepareForValidation(): void
    {
        $raw = $this->input('translations');
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->merge(['translations' => $decoded]);
            }
        }

        $remove = $this->input('removeImage');
        if ($remove === '1' || $remove === 'true' || $remove === true) {
            $this->merge(['removeImage' => true]);
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

        $rules = [
            'iconKey' => ['sometimes', 'string', 'max:64'],
            'sortOrder' => ['sometimes', 'integer', 'min:0', 'max:999999'],
            'translations' => ['sometimes', 'array'],
            'image' => ['sometimes', 'nullable', 'file', 'image', 'max:20480'],
            'removeImage' => ['sometimes', 'boolean'],
        ];

        foreach ($locales as $loc) {
            $prefix = "translations.$loc";
            $rules[$prefix] = ['sometimes', 'nullable', 'array'];
            $rules["$prefix.title"] = ['sometimes', 'nullable', 'string', 'max:500'];
            $rules["$prefix.description"] = ['sometimes', 'nullable', 'string'];
            $rules["$prefix.features"] = ['sometimes', 'nullable', 'array'];
            $rules["$prefix.features.*"] = ['string', 'max:500'];
            $rules["$prefix.seoTitle"] = ['sometimes', 'nullable', 'string', 'max:255'];
            $rules["$prefix.seoDescription"] = ['sometimes', 'nullable', 'string', 'max:8000'];
            $rules["$prefix.seoKeywords"] = ['sometimes', 'nullable', 'string', 'max:500'];
        }

        return $rules;
    }
}
