<?php

namespace App\Http\Requests\ApplicationForm;

use App\Models\ApplicationForm;
use App\Support\RequestedDocumentCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestDocumentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $form = $this->route('application_form');
        if (! $form instanceof ApplicationForm) {
            return false;
        }

        return $this->user()->can('update', $form);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $valid = RequestedDocumentCatalog::validKeys();

        return [
            'document_keys' => ['required', 'array', 'min:1'],
            'document_keys.*' => ['required', 'string', Rule::in($valid)],
        ];
    }

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator): void {
            $keys = $this->input('document_keys', []);
            if (! is_array($keys)) {
                return;
            }
            if (count($keys) !== count(array_unique($keys))) {
                $validator->errors()->add('document_keys', 'Выберите разные типы документов.');
            }
        });
    }
}
