<?php

namespace App\Http\Requests\SiteSeo;

use App\DTO\Common\PaginatedTableRequestDto;
use App\Support\AdminListQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexSiteSeoPagesRequest extends FormRequest
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
            'search' => ['sometimes', 'nullable', 'string', 'max:500'],
            'sort' => ['sometimes', 'string', Rule::in(['id', 'slug', 'label'])],
            'order' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    public function toPaginatedTableDto(): PaginatedTableRequestDto
    {
        return new PaginatedTableRequestDto(
            perPage: 1,
            page: 1,
            filters: array_merge(
                AdminListQuery::sortOrder($this, ['id', 'slug', 'label'], 'slug', 'asc'),
                array_filter(['search' => AdminListQuery::search($this)])
            ),
        );
    }
}
