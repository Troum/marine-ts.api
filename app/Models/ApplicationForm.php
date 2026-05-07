<?php

namespace App\Models;

use App\Enums\ApplicationFormStatus;
use App\Observers\ApplicationFormObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
    protected static function booted(): void
    {
        static::creating(function (ApplicationForm $model): void {
            if (! is_string($model->uuid) || $model->uuid === '') {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Имя PDF для вложений, скачивания и писем (anketa-{slug}_{uuid}.pdf).
     */
    public function pdfFileName(): string
    {
        $slug = Str::slug(trim((string) $this->full_name), '-', 'ru');
        if ($slug === '') {
            $slug = 'kandidat';
        }

        if (! is_string($this->uuid) || $this->uuid === '') {
            throw new \RuntimeException('ApplicationForm.uuid is required for PDF filename.');
        }

        return 'anketa-'.$slug.'_'.$this->uuid.'.pdf';
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
