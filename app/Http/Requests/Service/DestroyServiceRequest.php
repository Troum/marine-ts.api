<?php

namespace App\Http\Requests\Service;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class DestroyServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Service $service */
        $service = $this->route('service');

        return $this->user()->can('delete', $service);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
