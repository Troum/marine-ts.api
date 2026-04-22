<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'company',
    'position',
    'phone',
    'email',
    'vessel_types',
    'vessels_count',
    'vessel_flag',
    'main_ports',
    'required_services',
    'message',
    'source_page',
    'ip',
    'read_at',
])]
class PageInquiry extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'vessel_types' => 'array',
            'required_services' => 'array',
            'vessels_count' => 'integer',
        ];
    }
}
