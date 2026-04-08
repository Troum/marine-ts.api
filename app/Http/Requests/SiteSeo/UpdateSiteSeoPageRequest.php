<?php

namespace App\Http\Requests\SiteSeo;

use App\Models\SiteSeoPage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSeoPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $slug = $this->route('slug');
        $page = SiteSeoPage::query()->where('slug', $slug)->first();

        return $page && $this->user()->can('update', $page);
    }

    protected function prepareForValidation(): void
    {
        $default = (string) config('marine.default_locale');
        if (! $this->has('translations') && ($this->has('seoTitle') || $this->has('seo_title'))) {
            $this->merge([
                'translations' => [
                    $default => [
                        'label' => $this->input('label'),
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
            'translations' => ['sometimes', 'array'],
        ];

        foreach ($locales as $loc) {
            $prefix = "translations.$loc";
            $rules[$prefix] = ['sometimes', 'nullable', 'array'];
            $rules["$prefix.label"] = ['sometimes', 'nullable', 'string', 'max:255'];
            $rules["$prefix.seoTitle"] = ['sometimes', 'nullable', 'string', 'max:255'];
            $rules["$prefix.seoDescription"] = ['sometimes', 'nullable', 'string', 'max:8000'];
            $rules["$prefix.seoKeywords"] = ['sometimes', 'nullable', 'string', 'max:500'];
        }

        return $rules;
    }
}
