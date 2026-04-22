<?php

namespace App\Http\Requests\ApplicationForm;

use App\DTO\Common\PaginatedTableRequestDto;
use App\Models\ApplicationForm;
use App\Support\AdminListQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexApplicationFormsForVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', ApplicationForm::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:500'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'search' => ['sometimes', 'nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'nullable', 'string'],
            'sort' => ['sometimes', 'string', Rule::in(['id', 'created_at', 'updated_at', 'full_name', 'email', 'status'])],
            'order' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    public function toPaginatedTableDto(): PaginatedTableRequestDto
    {
        /** @var array<string, mixed> $v */
        $v = $this->validated();

        return new PaginatedTableRequestDto(
            perPage: min(max((int) ($v['per_page'] ?? 100), 1), 500),
            page: max(1, (int) ($v['page'] ?? 1)),
            filters: array_merge(
                AdminListQuery::sortOrder($this, ['id', 'created_at', 'updated_at', 'full_name', 'email', 'status'], 'id', 'desc'),
                array_filter([
                    'search' => AdminListQuery::search($this),
                    'status' => AdminListQuery::statusFilter($this),
                ])
            ),
        );
    }
}
