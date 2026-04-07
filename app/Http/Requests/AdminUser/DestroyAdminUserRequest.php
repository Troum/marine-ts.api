<?php

namespace App\Http\Requests\AdminUser;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class DestroyAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User $model */
        $model = $this->route('user');

        return $this->user()->can('delete', $model);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
