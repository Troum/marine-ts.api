<?php

namespace App\Http\Requests\News;

use App\DTO\Common\PaginatedTableRequestDto;
use App\Support\AdminListQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'sort' => ['sometimes', 'string', Rule::in(['id', 'title', 'date', 'category', 'author', 'slug'])],
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
                AdminListQuery::sortOrder($this, ['id', 'title', 'date', 'category', 'author', 'slug'], 'id'),
                array_filter(['search' => AdminListQuery::search($this)])
            ),
        );
    }
}
