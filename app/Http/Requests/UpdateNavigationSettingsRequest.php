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
            'main.*.children' => ['nullable', 'array', 'max:20'],
            'main.*.children.*.path' => ['required', 'string', 'max:500'],
            'main.*.children.*.label' => ['required', 'array'],
            'main.*.children.*.label.ru' => ['required', 'string', 'max:120'],
            'main.*.children.*.label.en' => ['required', 'string', 'max:120'],

            'more' => ['required', 'array', 'max:20'],
            'more.*.path' => ['required', 'string', 'max:500'],
            'more.*.label' => ['required', 'array'],
            'more.*.label.ru' => ['required', 'string', 'max:120'],
            'more.*.label.en' => ['required', 'string', 'max:120'],
            'more.*.children' => ['nullable', 'array', 'max:20'],
            'more.*.children.*.path' => ['required', 'string', 'max:500'],
            'more.*.children.*.label' => ['required', 'array'],
            'more.*.children.*.label.ru' => ['required', 'string', 'max:120'],
            'more.*.children.*.label.en' => ['required', 'string', 'max:120'],
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
                    if ($path === '#') {
                        $children = $item['children'] ?? [];
                        if (! is_array($children) || count($children) === 0) {
                            $validator->errors()->add($prefix, 'Для пути # нужна хотя бы одна вложенная ссылка.');
                        }

                        continue;
                    }
                    if (preg_match('#^https?://#i', $path)) {
                        continue;
                    }
                    if ($path !== '/' && ! preg_match('#^/[a-zA-Z0-9/_-]*$#', $path)) {
                        $validator->errors()->add($prefix, 'Путь должен начинаться с / (латиница, цифры, дефис, слэш), быть # при подменю без страницы, или укажите полный http(s) URL.');
                    }
                }

                foreach ($items as $i => $item) {
                    if (! is_array($item) || ! isset($item['children']) || ! is_array($item['children'])) {
                        continue;
                    }
                    foreach ($item['children'] as $ci => $child) {
                        if (! is_array($child)) {
                            continue;
                        }
                        $cpath = isset($child['path']) ? trim((string) $child['path']) : '';
                        $cprefix = "{$section}.{$i}.children.{$ci}.path";
                        if ($cpath === '' || $cpath === '#') {
                            $validator->errors()->add($cprefix, 'У вложенного пункта укажите обычный путь или URL (не #).');

                            continue;
                        }
                        if (preg_match('#^https?://#i', $cpath)) {
                            continue;
                        }
                        if ($cpath !== '/' && ! preg_match('#^/[a-zA-Z0-9/_-]*$#', $cpath)) {
                            $validator->errors()->add($cprefix, 'Путь должен начинаться с / (латиница, цифры, дефис, слэш) или укажите полный http(s) URL.');
                        }
                    }
                }
            }
        });
    }
}
