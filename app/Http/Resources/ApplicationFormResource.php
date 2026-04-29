<?php

namespace App\Http\Resources;

use App\Models\ApplicationForm;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ApplicationForm
 */
class ApplicationFormResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payload = is_array($this->payload) ? $this->payload : [];
        $hasPhoto = isset($payload['photoStoredPath']) && is_string($payload['photoStoredPath']) && $payload['photoStoredPath'] !== '';

        return [
            'id' => $this->id,
            'vacancyId' => $this->vacancy_id,
            'vacancyTitle' => $this->vacancy_id === null
                ? 'Открытая заявка (без вакансии)'
                : $this->whenLoaded('vacancy', function () {
                    $v = $this->vacancy;
                    if ($v === null) {
                        return null;
                    }
                    $v->loadMissing('translations');

                    return $v->translationForLocale((string) config('marine.default_locale'))?->title;
                }),
            'vacancySlug' => $this->whenLoaded('vacancy', fn () => $this->vacancy?->slug),
            'fullName' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status?->value,
            'payload' => $payload,
            'hasPhoto' => $hasPhoto,
            'requestedDocumentKeys' => $this->requested_document_keys ?? [],
            'documentUploadExpiresAt' => $this->document_upload_token_expires_at?->toIso8601String(),
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
