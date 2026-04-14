<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateNavigationSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('manage navigation');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'main' => ['required', 'array', 'min:1', 'max:20'],
            'main.*.path' => ['required', 'string', 'max:500'],
            'main.*.label' => ['required', 'array'],
            'main.*.label.ru' => ['required', 'string', 'max:120'],
            'main.*.label.en' => ['required', 'string', 'max:120'],

            'more' => ['required', 'array', 'max:20'],
            'more.*.path' => ['required', 'string', 'max:500'],
            'more.*.label' => ['required', 'array'],
            'more.*.label.ru' => ['required', 'string', 'max:120'],
            'more.*.label.en' => ['required', 'string', 'max:120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach (['main', 'more'] as $section) {
                $items = $this->input($section);
                if (! is_array($items)) {
                    continue;
                }
                foreach ($items as $i => $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $path = isset($item['path']) ? trim((string) $item['path']) : '';
                    $prefix = "{$section}.{$i}.path";
                    if ($path === '') {
                        $validator->errors()->add($prefix, 'Укажите путь или URL.');

                        continue;
                    }
                    if (preg_match('#^https?://#i', $path)) {
                        continue;
                    }
                    if ($path !== '/' && ! preg_match('#^/[a-zA-Z0-9/_-]*$#', $path)) {
                        $validator->errors()->add($prefix, 'Путь должен начинаться с / (латиница, цифры, дефис, слэш) или укажите полный http(s) URL.');
                    }
                }
            }
        });
    }
}
