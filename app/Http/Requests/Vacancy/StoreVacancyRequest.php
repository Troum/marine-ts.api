<?php

namespace App\Http\Requests\Vacancy;

use App\Models\Vacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Vacancy::class);
    }

    protected function prepareForValidation(): void
    {
        $default = (string) config('marine.default_locale');
        if (! $this->has('translations') && $this->has('title')) {
            $this->merge([
                'translations' => [
                    $default => [
                        'title' => $this->input('title'),
                        'excerpt' => $this->input('excerpt'),
                        'content' => $this->input('content'),
                        'requirements' => $this->input('requirements'),
                        'location' => $this->input('location'),
                        'employmentType' => $this->input('employmentType') ?? $this->input('employment_type'),
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
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('vacancies', 'slug')],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
            'isPublished' => ['sometimes', 'boolean'],
            'translations' => ['required', 'array'],
        ];

        foreach ($locales as $loc) {
            $isDefault = $loc === $default;
            $prefix = "translations.$loc";
            $rules[$prefix] = [$isDefault ? 'required' : 'nullable', 'array'];
            $rules["$prefix.title"] = [$isDefault ? 'required' : 'sometimes', 'nullable', 'string', 'max:500'];
            $rules["$prefix.excerpt"] = [$isDefault ? 'required' : 'sometimes', 'nullable', 'string'];
            $rules["$prefix.content"] = ['nullable', 'string'];
            $rules["$prefix.requirements"] = ['nullable', 'array'];
            $rules["$prefix.requirements.*"] = ['string', 'max:2000'];
            $rules["$prefix.location"] = ['nullable', 'string', 'max:255'];
            $rules["$prefix.employmentType"] = ['nullable', 'string', 'max:255'];
            $rules["$prefix.seoTitle"] = ['sometimes', 'nullable', 'string', 'max:255'];
            $rules["$prefix.seoDescription"] = ['sometimes', 'nullable', 'string', 'max:8000'];
            $rules["$prefix.seoKeywords"] = ['sometimes', 'nullable', 'string', 'max:500'];
            $rules["$prefix.seoImage"] = ['sometimes', 'nullable', 'string', 'max:2000'];
        }

        return $rules;
    }
}
