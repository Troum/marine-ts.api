<?php

namespace App\Http\Requests\Vacancy;

use App\Models\Vacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Vacancy $vacancy */
        $vacancy = $this->route('vacancy');

        return $this->user()->can('update', $vacancy);
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
        /** @var Vacancy $vacancy */
        $vacancy = $this->route('vacancy');

        $locales = config('marine.locales');
        if (! is_array($locales)) {
            $locales = ['ru', 'en'];
        }

        $rules = [
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('vacancies', 'slug')->ignore($vacancy->id)],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
            'isPublished' => ['sometimes', 'boolean'],
            'translations' => ['sometimes', 'array'],
        ];

        foreach ($locales as $loc) {
            $prefix = "translations.$loc";
            $rules[$prefix] = ['sometimes', 'nullable', 'array'];
            $rules["$prefix.title"] = ['sometimes', 'nullable', 'string', 'max:500'];
            $rules["$prefix.excerpt"] = ['sometimes', 'nullable', 'string'];
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
