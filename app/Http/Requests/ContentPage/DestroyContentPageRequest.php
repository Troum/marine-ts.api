<?php

namespace App\Http\Requests\ContentPage;

use App\Models\ContentPage;
use Illuminate\Foundation\Http\FormRequest;

class DestroyContentPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var ContentPage $page */
        $page = $this->route('content_page');

        return $this->user()->can('delete', $page);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
