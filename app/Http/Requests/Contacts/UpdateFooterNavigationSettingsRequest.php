<?php

namespace App\Http\Requests\Contacts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateFooterNavigationSettingsRequest extends FormRequest
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
            'columns'              => ['required', 'array', 'max:20'],
            'columns.*.hidden'     => ['sometimes', 'boolean'],
            'columns.*.title'      => ['sometimes', 'array'],
            'columns.*.title.ru'   => ['sometimes', 'nullable', 'string', 'max:80'],
            'columns.*.title.en'   => ['sometimes', 'nullable', 'string', 'max:80'],
            'columns.*.links'      => ['sometimes', 'array', 'max:30'],
            'columns.*.links.*.path'     => ['required', 'string', 'max:500'],
            'columns.*.links.*.label'    => ['required', 'array'],
            'columns.*.links.*.label.ru' => ['required', 'string', 'max:120'],
            'columns.*.links.*.label.en' => ['required', 'string', 'max:120'],

            'legal'            => ['required', 'array', 'max:10'],
            'legal.*.path'     => ['required', 'string', 'max:500'],
            'legal.*.label'    => ['required', 'array'],
            'legal.*.label.ru' => ['required', 'string', 'max:120'],
            'legal.*.label.en' => ['required', 'string', 'max:120'],

            'hideFooterGlobally' => ['sometimes', 'boolean'],
            'hideFooterPaths'    => ['sometimes', 'array', 'max:50'],
            'hideFooterPaths.*'  => ['string', 'max:200'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $columns = $this->input('columns');
            if (is_array($columns)) {
                foreach ($columns as $ci => $col) {
                    if (! is_array($col)) {
                        continue;
                    }

                    $hidden = isset($col['hidden']) && filter_var($col['hidden'], FILTER_VALIDATE_BOOLEAN);

                    // Visible columns must have titles and links.
                    if (! $hidden) {
                        foreach (['ru', 'en'] as $loc) {
                            $val = trim((string) ($col['title'][$loc] ?? ''));
                            if ($val === '') {
                                $validator->errors()->add(
                                    "columns.{$ci}.title.{$loc}",
                                    "Заголовок видимой колонки ({$loc}) обязателен."
                                );
                            }
                        }
                    }

                    if (isset($col['links']) && is_array($col['links'])) {
                        foreach ($col['links'] as $li => $link) {
                            $this->validatePath($validator, "columns.{$ci}.links.{$li}.path", is_array($link) ? $link : []);
                        }
                    }
                }
            }

            $legal = $this->input('legal');
            if (is_array($legal)) {
                foreach ($legal as $i => $link) {
                    $this->validatePath($validator, "legal.{$i}.path", is_array($link) ? $link : []);
                }
            }
        });
    }

    /**
     * @param  array<string, mixed>  $link
     */
    private function validatePath(Validator $validator, string $prefix, array $link): void
    {
        $path = isset($link['path']) ? trim((string) $link['path']) : '';
        if ($path === '' || $path === '#') {
            $validator->errors()->add($prefix, 'Укажите путь или полный URL (без #).');

            return;
        }
        if (preg_match('#^https?://#i', $path)) {
            return;
        }
        if ($path !== '/' && ! preg_match('#^/[a-zA-Z0-9/_-]*$#', $path)) {
            $validator->errors()->add($prefix, 'Путь должен начинаться с / или укажите полный http(s) URL.');
        }
    }
}
