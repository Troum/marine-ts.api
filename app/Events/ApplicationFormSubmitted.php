<?php

namespace App\Events;

use App\Models\ApplicationForm;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Анкета сохранена (включая опциональное прикрепление фото) — side effects (письма, интеграции) подписываются сюда.
 */
final class ApplicationFormSubmitted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public ApplicationForm $applicationForm,
    ) {}
}
