<?php

namespace App\Http\Requests\AdminUser;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class IndexAdminUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', User::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
