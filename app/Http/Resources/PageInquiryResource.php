<?php

namespace App\Http\Resources;

use App\Models\PageInquiry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin PageInquiry
 */
class PageInquiryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'company' => $this->company,
            'position' => $this->position,
            'phone' => $this->phone,
            'email' => $this->email,
            'vesselTypes' => self::asStringList($this->vessel_types),
            'vesselsCount' => $this->vessels_count,
            'vesselFlag' => $this->vessel_flag,
            'mainPorts' => $this->main_ports,
            'requiredServices' => self::asStringList($this->required_services),
            'message' => $this->message,
            'sourcePage' => $this->source_page,
            'ip' => $this->ip,
            'readAt' => self::toIso8601($this->read_at),
            'createdAt' => self::toIso8601($this->created_at),
            'updatedAt' => self::toIso8601($this->updated_at),
        ];
    }

    /**
     * @return list<string>
     */
    private static function asStringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $out = [];
        foreach ($value as $item) {
            if (is_string($item) && $item !== '') {
                $out[] = $item;
            }
        }

        return array_values($out);
    }

    private static function toIso8601(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return Carbon::parse($value)->toIso8601String();
    }
}
