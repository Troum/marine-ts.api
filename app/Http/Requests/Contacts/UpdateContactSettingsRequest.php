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
        $departments = $this->input('departments');

        $data = [];
        if (is_array($quick)) {
            $data['quick'] = array_map(static function ($row) {
                if (! is_array($row)) {
                    return $row;
                }

                if (! isset($row['iconKey']) && isset($row['icon_key'])) {
                    $row['iconKey'] = $row['icon_key'];
                } elseif (! isset($row['iconKey']) && isset($row['icon'])) {
                    $row['iconKey'] = $row['icon'];
                }
                if (! isset($row['showInFooter']) && isset($row['show_in_footer'])) {
                    $row['showInFooter'] = $row['show_in_footer'];
                }

                return $row;
            }, $quick);
        }

        if (is_array($departments)) {
            $data['departments'] = array_map(static function ($row) {
                if (! is_array($row) || isset($row['showInFooter']) || ! isset($row['show_in_footer'])) {
                    return $row;
                }

                $row['showInFooter'] = $row['show_in_footer'];

                return $row;
            }, $departments);
        }

        if ($data !== []) {
            $this->merge($data);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'quick' => ['required', 'array', 'min:1', 'max:20'],
            'quick.*.iconKey' => ['required', 'string', Rule::in(['phone', 'mail', 'map-pin', 'clock', 'link', 'linkedin', 'vk', 'max'])],
            'quick.*.label' => ['required', 'string', 'max:120'],
            'quick.*.value' => ['required', 'string', 'max:500'],
            'quick.*.href' => ['nullable', 'string', 'max:500'],
            'quick.*.showInFooter' => ['sometimes', 'boolean'],

            'departments' => ['sometimes', 'array', 'max:30'],
            'departments.*.title' => ['required', 'string', 'max:160'],
            'departments.*.phone' => ['required', 'string', 'max:120'],
            'departments.*.email' => ['required', 'string', 'email', 'max:120'],
            'departments.*.showInFooter' => ['sometimes', 'boolean'],

            'offices' => ['required', 'array', 'min:1', 'max:30'],
            'offices.*.city' => ['required', 'string', 'max:120'],
            'offices.*.country' => ['required', 'string', 'max:120'],
            'offices.*.address' => ['required', 'string', 'max:500'],
            'offices.*.phone' => ['required', 'string', 'max:120'],
            'offices.*.email' => ['required', 'string', 'email', 'max:120'],
        ];
    }
}
