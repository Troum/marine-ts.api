<?php

namespace App\Http\Requests\ContentPage;

use App\DTO\Common\PaginatedTableRequestDto;
use App\Models\ContentPage;
use App\Support\AdminListQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexContentPagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', ContentPage::class);
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
            'published' => ['sometimes', 'nullable'],
            'sort' => ['sometimes', 'string', Rule::in(['id', 'slug', 'title', 'sort_order', 'created_at', 'updated_at'])],
            'order' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    public function toPaginatedTableDto(): PaginatedTableRequestDto
    {
        /** @var array<string, mixed> $v */
        $v = $this->validated();

        $published = AdminListQuery::publishedTriState($this);
        $filters = array_merge(
            AdminListQuery::sortOrder($this, ['id', 'slug', 'title', 'sort_order', 'created_at', 'updated_at'], 'sort_order', 'asc'),
            array_filter(['search' => AdminListQuery::search($this)])
        );
        if ($published !== null) {
            $filters['published_filter'] = $published;
        }

        $filters['exclude_slugs'] = ['home', 'about', 'crewing-management', 'contacts', 'services', 'projects', 'gallery'];

        return new PaginatedTableRequestDto(
            perPage: min(max((int) ($v['per_page'] ?? 100), 1), 500),
            page: max(1, (int) ($v['page'] ?? 1)),
            filters: $filters,
        );
    }
}
