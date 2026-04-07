<?php

namespace App\Http\Requests\ContentPage;

use App\Models\ContentPage;
use Illuminate\Foundation\Http\FormRequest;

class ShowContentPageManageRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var ContentPage $page */
        $page = $this->route('content_page');

        return $this->user()->can('view', $page);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
