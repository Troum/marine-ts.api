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

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Vacancy $vacancy */
        $vacancy = $this->route('vacancy');

        return [
            'title' => ['sometimes', 'string', 'max:500'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('vacancies', 'slug')->ignore($vacancy->id)],
            'excerpt' => ['sometimes', 'string'],
            'content' => ['nullable', 'string'],
            'requirements' => ['nullable', 'array'],
            'requirements.*' => ['string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
            'employmentType' => ['nullable', 'string', 'max:255'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
            'isPublished' => ['sometimes', 'boolean'],
            'seoTitle' => ['sometimes', 'nullable', 'string', 'max:255'],
            'seoDescription' => ['sometimes', 'nullable', 'string', 'max:8000'],
            'seoKeywords' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}
