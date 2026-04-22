<?php

namespace App\Http\Requests\ContentPage;

use Illuminate\Foundation\Http\FormRequest;

class PublicContentPageIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
