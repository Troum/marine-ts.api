{{ config('app.name') }} — новая анкета #{{ $applicationForm->id }}
────────────────────────────────────────

Здравствуйте.

Поступила новая анкета кандидата {{ $applicationForm->full_name }} (ID {{ $applicationForm->id }}).

Копия данных во вложении в формате PDF (anketa-{{ $applicationForm->id }}.pdf).

────────────────────────────────────────
Внутреннее уведомление · {{ config('app.name') }}
