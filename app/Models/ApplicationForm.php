<?php

namespace App\Models;

use App\Enums\ApplicationFormStatus;
use App\Observers\ApplicationFormObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([ApplicationFormObserver::class])]
#[Fillable([
    'vacancy_id',
    'status',
    'full_name',
    'email',
    'phone',
    'payload',
    'document_upload_token_hash',
    'document_upload_token_expires_at',
    'requested_document_keys',
])]
class ApplicationForm extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ApplicationFormStatus::class,
            'payload' => 'array',
            'requested_document_keys' => 'array',
            'document_upload_token_expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Vacancy, $this>
     */
    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }
}
