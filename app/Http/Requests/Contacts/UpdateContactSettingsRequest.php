<?php

namespace App\Http\Requests\Contacts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactSettingsRequest extends FormRequest
{
    /**
     * @param  \Closure(string): void  $fail
     */
    private static function validateLocalizedLine(string $attribute, mixed $value, \Closure $fail, int $max, bool $requireNonEmpty): void
    {
        if (is_string($value)) {
            if (strlen($value) > $max) {
                $fail(__('validation.max.string', ['attribute' => $attribute, 'max' => $max]));

                return;
            }
            if ($requireNonEmpty && trim($value) === '') {
                $fail(__('validation.required', ['attribute' => $attribute]));
            }

            return;
        }

        if (! is_array($value)) {
            $fail(__('validation.string', ['attribute' => $attribute]));

            return;
        }

        $ru = isset($value['ru']) && is_string($value['ru']) ? $value['ru'] : '';
        $en = isset($value['en']) && is_string($value['en']) ? $value['en'] : '';

        if (strlen($ru) > $max || strlen($en) > $max) {
            $fail(__('validation.max.string', ['attribute' => $attribute, 'max' => $max]));

            return;
        }

        if ($requireNonEmpty && trim($ru) === '' && trim($en) === '') {
            $fail(__('validation.required', ['attribute' => $attribute]));
        }
    }

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
            'quick.*.label' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    self::validateLocalizedLine($attribute, $value, $fail, 120, true);
                },
            ],
            'quick.*.value' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    self::validateLocalizedLine($attribute, $value, $fail, 500, true);
                },
            ],
            'quick.*.href' => ['nullable', 'string', 'max:500'],
            'quick.*.showInFooter' => ['sometimes', 'boolean'],

            'socials' => ['sometimes', 'nullable', 'array', 'max:20'],
            'socials.*.iconKey' => ['required', 'string', 'max:40'],
            'socials.*.url' => ['required', 'string', 'max:500'],

            'departments' => ['sometimes', 'array', 'max:30'],
            'departments.*.title' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    self::validateLocalizedLine($attribute, $value, $fail, 160, true);
                },
            ],
            'departments.*.phone' => ['required', 'string', 'max:120'],
            'departments.*.email' => ['required', 'string', 'email', 'max:120'],
            'departments.*.showInFooter' => ['sometimes', 'boolean'],

            'offices' => ['required', 'array', 'min:1', 'max:30'],
            'offices.*.city' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    self::validateLocalizedLine($attribute, $value, $fail, 120, true);
                },
            ],
            'offices.*.country' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    self::validateLocalizedLine($attribute, $value, $fail, 120, true);
                },
            ],
            'offices.*.address' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    self::validateLocalizedLine($attribute, $value, $fail, 2000, true);
                },
            ],
            'offices.*.phone' => ['required', 'string', 'max:120'],
            'offices.*.email' => ['required', 'string', 'email', 'max:120'],
        ];
    }
}
