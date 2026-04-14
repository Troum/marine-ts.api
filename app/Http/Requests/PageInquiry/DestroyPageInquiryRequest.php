<?php

namespace App\Http\Requests\PageInquiry;

use App\Models\PageInquiry;
use Illuminate\Foundation\Http\FormRequest;

class DestroyPageInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var PageInquiry $inquiry */
        $inquiry = $this->route('page_inquiry');

        return $this->user()->can('delete', $inquiry);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
