<?php

namespace App\Http\Resources;

use App\Models\FeedbackMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin FeedbackMessage
 */
class FeedbackResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'message' => $this->message,
            'ip' => $this->ip,
            'readAt' => self::toIso8601($this->read_at),
            'repliedAt' => self::toIso8601($this->replied_at),
            'createdAt' => self::toIso8601($this->created_at),
            'updatedAt' => self::toIso8601($this->updated_at),
        ];
    }

    private static function toIso8601(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return Carbon::parse($value)->toIso8601String();
    }
}
