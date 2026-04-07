<?php

namespace App\Http\Requests\ApplicationForm;

use App\Enums\ApplicationFormStatus;
use App\Models\ApplicationForm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicationFormStatusRequest extends FormRequest
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
        return [
            'status' => [
                'required',
                Rule::enum(ApplicationFormStatus::class)->only([
                    ApplicationFormStatus::Accepted,
                    ApplicationFormStatus::Rejected,
                ]),
            ],
        ];
    }
}
