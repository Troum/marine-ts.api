<?php

namespace App\Http\Requests\Project;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Project $project */
        $project = $this->route('project');

        return $this->user()->can('update', $project);
    }

    protected function prepareForValidation(): void
    {
        $default = (string) config('marine.default_locale');
        if (! $this->has('translations') && $this->has('title')) {
            $this->merge([
                'translations' => [
                    $default => [
                        'title' => $this->input('title'),
                        'typeLabel' => $this->input('typeLabel') ?? $this->input('type_label'),
                        'location' => $this->input('location'),
                        'description' => $this->input('description'),
                        'stats' => $this->input('stats'),
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
            'type' => ['sometimes', 'string', 'in:hull,engine,electrical'],
            'date' => ['sometimes', 'string', 'max:32'],
            'image' => ['nullable', 'string', 'max:500'],
            'translations' => ['sometimes', 'array'],
        ];

        foreach ($locales as $loc) {
            $prefix = "translations.$loc";
            $rules[$prefix] = ['sometimes', 'nullable', 'array'];
            $rules["$prefix.title"] = ['sometimes', 'nullable', 'string', 'max:500'];
            $rules["$prefix.typeLabel"] = ['sometimes', 'nullable', 'string', 'max:255'];
            $rules["$prefix.location"] = ['sometimes', 'nullable', 'string', 'max:255'];
            $rules["$prefix.description"] = ['sometimes', 'nullable', 'string'];
            $rules["$prefix.stats"] = ['sometimes', 'nullable', 'array'];
            $rules["$prefix.seoTitle"] = ['sometimes', 'nullable', 'string', 'max:255'];
            $rules["$prefix.seoDescription"] = ['sometimes', 'nullable', 'string', 'max:8000'];
            $rules["$prefix.seoKeywords"] = ['sometimes', 'nullable', 'string', 'max:500'];
        }

        return $rules;
    }
}
