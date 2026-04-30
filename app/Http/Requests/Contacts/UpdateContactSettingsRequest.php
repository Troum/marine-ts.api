<?php

namespace App\Http\Requests\Contacts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('manage contacts');
    }

    protected function prepareForValidation(): void
    {
        $quick = $this->input('quick');
        if (! is_array($quick)) {
            return;
        }

        $this->merge([
            'quick' => array_map(static function ($row) {
                if (! is_array($row) || isset($row['iconKey'])) {
                    return $row;
                }

                if (isset($row['icon_key'])) {
                    $row['iconKey'] = $row['icon_key'];
                } elseif (isset($row['icon'])) {
                    $row['iconKey'] = $row['icon'];
                }

                return $row;
            }, $quick),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'quick' => ['required', 'array', 'min:1', 'max:20'],
            'quick.*.iconKey' => ['required', 'string', Rule::in(['phone', 'mail', 'map-pin', 'clock', 'link'])],
            'quick.*.label' => ['required', 'string', 'max:120'],
            'quick.*.value' => ['required', 'string', 'max:500'],
            'quick.*.href' => ['nullable', 'string', 'max:500'],

            'offices' => ['required', 'array', 'min:1', 'max:30'],
            'offices.*.city' => ['required', 'string', 'max:120'],
            'offices.*.country' => ['required', 'string', 'max:120'],
            'offices.*.address' => ['required', 'string', 'max:500'],
            'offices.*.phone' => ['required', 'string', 'max:120'],
            'offices.*.email' => ['required', 'string', 'email', 'max:120'],
        ];
    }
}
