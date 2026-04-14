<?php

namespace App\Http\Requests\PageInquiry;

use Illuminate\Foundation\Http\FormRequest;

class StorePageInquiryRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'company' => ['nullable', 'string', 'max:255'],
            'vessel_name' => ['nullable', 'string', 'max:255'],
            'imo' => ['nullable', 'string', 'max:32'],
            'message' => ['required', 'string', 'max:10000'],
            'source_page' => ['required', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'sourcePage' => 'source_page',
            'vesselName' => 'vessel_name',
        ];
        foreach ($map as $camel => $snake) {
            if ($this->has($camel) && ! $this->has($snake)) {
                $this->merge([$snake => $this->input($camel)]);
            }
        }
    }
}
