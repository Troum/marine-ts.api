<?php

namespace App\Http\Requests\PageInquiry;

use App\DTO\Common\PaginatedTableRequestDto;
use App\Models\PageInquiry;
use App\Support\AdminListQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexPageInquiriesManageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', PageInquiry::class);
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
            'read' => ['sometimes', 'nullable'],
            'sort' => ['sometimes', 'string', Rule::in(['id', 'created_at', 'updated_at', 'read_at'])],
            'order' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    public function toPaginatedTableDto(): PaginatedTableRequestDto
    {
        /** @var array<string, mixed> $v */
        $v = $this->validated();

        $filters = array_merge(
            AdminListQuery::sortOrder($this, ['id', 'created_at', 'updated_at', 'read_at'], 'id', 'desc'),
            array_filter([
                'search' => AdminListQuery::search($this),
            ])
        );
        $filters['read'] = AdminListQuery::readTriState($this);

        return new PaginatedTableRequestDto(
            perPage: min(max((int) ($v['per_page'] ?? 500), 1), 500),
            page: max(1, (int) ($v['page'] ?? 1)),
            filters: $filters,
        );
    }
}
