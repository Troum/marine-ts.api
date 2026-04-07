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

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'seoTitle' => ['nullable', 'string', 'max:255'],
            'seoDescription' => ['nullable', 'string', 'max:8000'],
            'seoKeywords' => ['nullable', 'string', 'max:500'],
        ];
    }
}
