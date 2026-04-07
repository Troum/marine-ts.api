<?php

namespace App\Http\Requests\ContentPage;

use App\Models\ContentPage;
use Illuminate\Foundation\Http\FormRequest;

class IndexContentPagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', ContentPage::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
