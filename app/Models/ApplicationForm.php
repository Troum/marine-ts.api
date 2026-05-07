<?php

namespace App\Models;

use App\Enums\ApplicationFormStatus;
use App\Support\ApplicationFormPdfFilename;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
    protected static function booted(): void
    {
        static::creating(function (ApplicationForm $model): void {
            if (! is_string($model->uuid) || $model->uuid === '') {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Имя PDF: lastName_firstName_pos_…должности…_ship_…типы_судов…_shortUuid.pdf
     */
    public function pdfFileName(): string
    {
        return ApplicationFormPdfFilename::build($this);
    }

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
